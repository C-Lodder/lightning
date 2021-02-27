<?php
/**
 * Automatic Image Resizer
 *
 * @copyright Copyright (c)2020 Nicholas K. Dionysopoulos
 * @author    Nicholas K. Dionysopoulos <nicholas@dionysopoulos.me>
 * @license   MIT
 * @version   1.0.2
 *
 * Your server must have the PHP GD extension installed and enabled.
 */

namespace Joomla\Template\Lightning;

use Exception;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Automatic Image Resizer for Joomla! 3 templates
 */
class ImageResizer
{
	private const OG_WIDTH = 1200;

	private const TWITTER_WIDTH = 800;

	/**
	 * Static instance of the ImageResizer object
	 *
	 * @var   self
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Does PHP seem to have GD installed, enabled and its functions not disabled?
	 *
	 * @var   bool|null
	 * @since 1.0.0
	 */
	private static $hasGD = null;

	/**
	 * Allowed image types to resize
	 *
	 * @var   string[]
	 * @since 1.0.0
	 */
	private $allowedTypes = ['png', 'jpg', 'gif'];

	/**
	 * Image breakpoints for self::getResizedSources
	 *
	 * @var   array
	 * @since 1.0.0
	 *
	 * @see   self::setBreakPoints
	 */
	private $breakPoints = [
		'(min-width: 665px)' => 852,
		'(min-width: 485px)' => 635,
		''                   => 453,
	];

	/**
	 * The subdirectory of the site's images directory where the resized images will be stored.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	private $relativeCachePath = 'resized';

	/**
	 * Should I sharpen the downsized image?
	 *
	 * This prevents the "bluriness" of downsized images. Disable at your discretion.
	 *
	 * @var   bool
	 * @since 1.0.0
	 */
	private $sharpen = true;

	/**
	 * Resized image quality, 0-100
	 *
	 * This is used verbatim for WebP and JPEG. It's converted to a compression scale of 0-9 (100 maps to 0) for PNG.
	 * Completely ignored for GIF and other formats.
	 *
	 * @var   int
	 * @since 1.0.0
	 */
	private $quality = 80;

	/**
	 * Returns a static instance of this helper
	 *
	 * @return  ImageResizer
	 * @since   1.0.0
	 */
	public static function getInstance(): ImageResizer
	{
		if (empty(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Set the image breakpoints.
	 *
	 * Breakpoints are given as an array. The key is a media query (or blank), the value is an integer.
	 * For example:
	 * ```php
	 * ImageResizer::getInstance()->setBreakPoints([
	 *      '(min-width: 1200px)' => 700, // Viewport 1200px and up, image size 700px
	 *      '(min-width: 980px)' => 540, // Viewport 980px and up, image size 540px
	 *      '(min-width: 768px)' => 700, // Viewport 768px and up, image size 700px
	 *      '(min-width: 575px)' => 540, // Viewport 575px and up, image size 540px
	 *      '' => 400 // Fallback to image size 400px
	 * ]);
	 * ```
	 *
	 * @param   array  $breakPoints
	 *
	 * @return  ImageResizer
	 * @since   1.0.0
	 */
	public function setBreakPoints(array $breakPoints): ImageResizer
	{
		$this->breakPoints = $breakPoints;

		return $this;
	}

	/**
	 * Set the resized image path relative to the images directory
	 *
	 * @param   string  $relativeCachePath
	 *
	 * @return  ImageResizer
	 * @since   1.0.0
	 */
	public function setRelativeCachePath(string $relativeCachePath): ImageResizer
	{
		$this->relativeCachePath = trim($relativeCachePath, '/');

		$absolutePath = JPATH_ROOT . '/media/cache/' . $this->relativeCachePath;

		if (!@is_dir($absolutePath))
		{
			@mkdir($absolutePath, 0755, true);
		}

		return $this;
	}

	/**
	 * Set the allowed image file extensions
	 *
	 * This needs to be a simple array of lowercase extensions without a dot.
	 *
	 * @param   string[]  $allowedTypes
	 *
	 * @return  ImageResizer
	 * @since   1.0.0
	 */
	public function setAllowedTypes(array $allowedTypes): ImageResizer
	{
		$this->allowedTypes = $allowedTypes;

		return $this;
	}

	/**
	 * Should I sharpen downscaled images?
	 *
	 * @param   bool  $sharpen
	 *
	 * @return  ImageResizer
	 * @since   1.0.0
	 */
	public function setSharpen(bool $sharpen): ImageResizer
	{
		$this->sharpen = $sharpen;

		return $this;
	}

	/**
	 * Set the quality of saved JPEG and WebP files
	 *
	 * @param   int  $quality
	 *
	 * @return  ImageResizer
	 * @since   1.0.0
	 */
	public function setQuality(int $quality): ImageResizer
	{
		$this->quality = max(min($quality, 100), 100);

		return $this;
	}

	public function getImageSize(string $url): array
	{
		// Is GD enabled?
		if (!$this->hasGD())
		{
			return [null, null];
		}

		// Is it a supported image type?
		$imageType = $this->getNormalizedExtension($url);

		if (!in_array($imageType, $this->allowedTypes))
		{
			return [null, null];
		}

		// Convert the file URL to a file path relative to the site's root
		$filePath = $this->resolveInternalLink($url);

		// If the link wasn't relative to our site's root we can't proceed.
		if (is_null($filePath))
		{
			return [null, null];
		}

		// Does the file exist?
		$absoluteFilePath = JPATH_ROOT . '/' . $filePath;

		if (!@file_exists($absoluteFilePath) || !@is_file($absoluteFilePath) || !@is_readable($absoluteFilePath))
		{
			return [null, null];
		}

		$dimensions = getimagesize($absoluteFilePath);

		return [$dimensions[0] ?? null, $dimensions[1] ?? null];
	}

	/**
	 * Get the resized resources for a particular image
	 *
	 * @param   string  $url     The URL to the image to resize
	 * @param   bool    $toWebP  Should I force conversion to WebP?
	 *
	 * @return  array|null as [$sizesString, $srcSetString, $urlOfSmallestImage]
	 * @since   1.0.0
	 */
	public function getResizedSources(string $url, bool $toWebP = false): ?array
	{
		// Is GD enabled?
		if (!$this->hasGD())
		{
			return ['', '', $url];
		}

		// Is it a supported image type?
		$imageType = $this->getNormalizedExtension($url);

		if (!in_array($imageType, $this->allowedTypes))
		{
			return ['', '', $url];
		}

		// Convert the file URL to a file path relative to the site's root
		$filePath = $this->resolveInternalLink($url);

		// If the link wasn't relative to our site's root we can't proceed.
		if (is_null($filePath))
		{
			return ['', '', $url];
		}

		// Does the file exist?
		$absoluteFilePath = JPATH_ROOT . '/' . $filePath;

		if (!@file_exists($absoluteFilePath) || !@is_file($absoluteFilePath) || !@is_readable($absoluteFilePath))
		{
			return ['', '', $url];
		}

		// Create image cache paths for each width using the appropriate file type (WebP or original image type)
		$images                 = [];
		$originalImageTimestamp = filemtime($absoluteFilePath);

		$dimensions = getimagesize($absoluteFilePath);
		$width      = $dimensions[0];
		$minWidth   = $width;

		foreach ($this->breakPoints as $mediaQuery => $pixelSize)
		{
			// If the image is too small for the requested width skip over this width.
			if (!empty($mediaQuery) && ($pixelSize > $width))
			{
				continue;
			}

			// Don't resize to the same width
			if (isset($images[$pixelSize]))
			{
				continue;
			}

			// Get the path to the resized image
			$resizedPath         = $this->getCachePath(
				$filePath, $pixelSize, $toWebP ? 'webp' : $imageType
			);
			$absoluteResizedPath = JPATH_ROOT . '/' . $resizedPath;

			// Create / update the resized image as needed
			if (
				$this->isOutdatedCache($absoluteResizedPath, $originalImageTimestamp)
				&& !$this->resizeImage($filePath, $absoluteResizedPath, $pixelSize)
			)
			{
				return ['', '', $url];
			}

			$images[$pixelSize] = $resizedPath;

			// Update the max width we've already created
			$minWidth = min($minWidth, $pixelSize);
		}

		$ext = $toWebP ? '.webp' : '.jpg';

		if (!isset($images[$width]) && (substr($url, -strlen($ext)) === $ext))
		{
			$images[$width] = $url;
		}

		$sizes = implode(', ', array_map(function ($mediaQuery, $size) {
			return $mediaQuery . ' ' . $size . 'px';
		}, array_keys($this->breakPoints), array_values($this->breakPoints)));
		$sizes = rtrim($sizes, ', ');

		$srcSet = implode(', ', array_map(function ($src, $pixelSize) {
			return trim(Uri::root(false) . $src . ' ' . $pixelSize . 'w');
		}, array_values($images), array_keys($images)));

		return [$sizes, $srcSet, $images[$minWidth] ?? $url];
	}

	/**
	 * Sets the og:image and twitter:image meta tags given a path or URL to an image file.
	 *
	 * If no image is specified, it doesn't exist, GD is not enabled, resized images cannot be written etc nothing will
	 * happen, of course.
	 *
	 * Article images have predetermined widths. See the class constants.
	 *
	 * We always use WebP images for articles per web best practices. Social media sites and search engines know how to
	 * convert them to a format suitable for the user's device.
	 *
	 * If the source image is very small you might end up with an OpenGraph and Twitter image pointing to the same file.
	 * In this case we only set the OpenGraph image to prevent unnecessary meta information duplication; Twitter is
	 * perfectly capable of picking up the OpenGraph image.
	 *
	 * @param   HtmlDocument  $doc
	 * @param   string|null   $image
	 *
	 * @throws  Exception
	 * @since   1.0.0
	 */
	public function setArticleImages(HtmlDocument $doc, ?string $image): void
	{
		if (empty($image))
		{
			return;
		}

		/** @var SiteApplication $app */
		$app = Factory::getApplication();

		if (!($app instanceof SiteApplication))
		{
			return;
		}

		$template = $app->getTemplate(true);

		[$sizes, $srcSet, $imgSrc] = (clone $this)
			->setRelativeCachePath($template->params->get('imgCacheFolder', 'autosized'))
			->setBreakPoints([
				sprintf("(min-width: %dw)", self::OG_WIDTH)      => self::OG_WIDTH,
				sprintf("(min-width: %dw)", self::TWITTER_WIDTH) => self::TWITTER_WIDTH,
			])
			->getResizedSources($image, true);

		$allImages = [];

		foreach (explode(', ', $srcSet) as $imgDef)
		{
			[$imgUrl, $imgSize] = explode(' ', $imgDef);
			$imgSize             = intval($imgSize);
			$allImages[$imgSize] = $imgUrl;
		}

		$ogImage      = $allImages[self::OG_WIDTH] ?? (Uri::root(false) . $image);
		$twitterImage = $allImages[self::TWITTER_WIDTH] ?? (Uri::root(false) . $image);

		$doc->setMetaData('og:image', $ogImage);

		// Only set a separate twitter:image meta if the OpenGraph image (og:image) is different than the Twitter image.
		if ($ogImage != $twitterImage)
		{
			$doc->setMetaData('twitter:image', $twitterImage);
		}
	}

	public function getImageFileType(?string $file): ?string
	{
		if (empty($file))
		{
			return null;
		}

		$extension = $this->getNormalizedExtension($file);

		switch ($extension)
		{
			case 'jpg':

				return 'image/jpeg';

				break;

			case 'png':

				return 'image/png';

				break;

			case 'gif':

				return 'image/gif';

				break;

			case 'webp':

				return 'image/webp';

				break;

			case 'svg':

				return 'image/svg+xml';

				break;

			case 'bmp':

				return 'image/bmp';

				break;

			case 'ico':

				return 'image/vnd.microsoft.icon';

				break;

			case 'tif':
			case 'tiff':

				return 'image/tiff';

				break;

			default:
				return null;
		}
	}

	/**
	 * Is the PHP-GD extension installed, enabled and its functions I need to use not disabled in php.ini?
	 *
	 * @return  bool
	 * @since   1.0.0
	 */
	private function hasGD(): bool
	{
		if (!is_null(self::$hasGD))
		{
			return self::$hasGD;
		}

		$functions = [
			'imagecreatefrompng', 'imagecreatefromgif', 'imagecreatefromjpeg', 'imageinterlace', 'getimagesize',
			'imagealphablending', 'imagesavealpha', 'imagecolorallocatealpha', 'imagefilledrectangle',
			'imagecopyresampled', 'imagedestroy',
		];

		self::$hasGD = true;

		foreach ($functions as $function)
		{
			if (!function_exists($function))
			{
				self::$hasGD = false;

				return false;
			}
		}

		return true;
	}

	/**
	 * Get the image type by extension
	 *
	 * The extension case does not matter.
	 *
	 * @param   string  $file
	 *
	 * @return  string|null
	 * @since   1.0.0
	 */
	private function getNormalizedExtension(string $file): ?string
	{
		if (empty($file))
		{
			return null;
		}

		$extension = pathinfo($file, PATHINFO_EXTENSION);

		switch ($extension)
		{
			// JPEG files come in different extensions
			case 'jpg':
			case 'jpe':
			case 'jpeg':
				return 'jpg';
				break;

			default:
				return $extension;
				break;
		}
	}

	/**
	 * Resolves an absolute/relative file URL to a file path relative to the site root.
	 *
	 * If it's not an internal URL it returns null;
	 *
	 * @param   string  $fileURL
	 *
	 * @return  string|null
	 * @since   1.0.0
	 */
	private function resolveInternalLink(string $fileURL): ?string
	{
		$currentUri = Uri::getInstance();

		// Convert a protocol-relative URL to an absolute one (so I can process it with the Joomla URI helper)
		if (substr($fileURL, 0, 2) == '//')
		{
			$fileURL = $currentUri->getScheme() . ':' . $fileURL;
		}

		if (strpos($fileURL, '://') !== false)
		{
			$uri = Uri::getInstance($fileURL);
			$uri->setScheme($currentUri->getScheme());

			$absoluteFileUri = $uri->toString();

			if (strpos($absoluteFileUri, Uri::base(false)) !== 0)
			{
				return null;
			}

			$path = $uri->getPath();
		}
		else
		{
			// Relative URL, remove the media query string
			[$path,] = explode('?', $fileURL, 2);
		}

		$rootPath = Uri::root(true);

		if (empty($rootPath))
		{
			return trim($path, '/');
		}

		return trim(substr($path, strlen($rootPath)), '/');
	}

	/**
	 * Returns the path of the resized image
	 *
	 * @param   string  $filePath          Relative path of original image to the site's root
	 * @param   int     $pixelSize         Desired width of the resized image in pixels
	 * @param   string  $desiredExtension  Desired extension of the resized image
	 *
	 * @return  string
	 * @since   1.0.0
	 */
	private function getCachePath(string $filePath, int $pixelSize, string $desiredExtension): string
	{
		$baseName = basename($filePath);
		$bits     = explode('.', $baseName);

		array_pop($bits);

		$beforeExtension = array_pop($bits);
		$bits[]          = $beforeExtension . '-' . $pixelSize;
		$bits[]          = $desiredExtension;
		$baseName        = implode('.', $bits);

		return implode('/', [
			'media/cache', $this->relativeCachePath, dirname($filePath), $baseName,
		]);
	}

	/**
	 * Is the resized image file out of date compared to the original?
	 *
	 * If the file doesn't exist at all or its timestamp is earlier than the original file's we return true.
	 *
	 * @param   string  $filePath   Relative filesystem path of the cache file
	 * @param   int     $timeStamp  File modification time of the original image
	 *
	 * @return  bool
	 * @since   1.0.0
	 */
	private function isOutdatedCache(string $filePath, int $timeStamp): bool
	{
		if (!@file_exists($filePath))
		{
			return true;
		}

		return filemtime($filePath) < $timeStamp;
	}

	/**
	 * Resizes an image to the specified width
	 *
	 * @param   string  $sourceFile  Absolute filesystem path to the original image file
	 * @param   string  $cacheFile   Absolute filesystem path to the resized image file
	 * @param   int     $newWidth    Requested image width, in pixels
	 *
	 * @return  bool
	 * @since   1.0.0
	 */
	private function resizeImage(string $sourceFile, string $cacheFile, int $newWidth): bool
	{
		$sourceType = $this->getNormalizedExtension($sourceFile);
		$cacheType  = $this->getNormalizedExtension($cacheFile);

		// Can I write to the cache file / folder?
		$cacheDir = dirname($cacheFile);

		if (!@is_dir($cacheDir) && !@mkdir($cacheDir, 0755, true))
		{
			return false;
		}

		if ((@file_exists($cacheFile) && !is_writeable($cacheFile)) || !@is_writable($cacheDir))
		{
			return false;
		}

		// Check the image dimensions
		$dimensions = getimagesize($sourceFile);
		$width      = $dimensions[0];
		$height     = $dimensions[1];

		// Do we need to downscale the image?
		if (($width <= $newWidth) && ($sourceType == $cacheType))
		{
			// No need. Just copy it over. TODO This code should not need to run if I catch this condition in the parent method and remove this resolution from the list.
			return @copy($sourceFile, $cacheFile);
		}

		// We need to resize the source image to the width of the resolution breakpoint we're working with
		$ratio     = $height / $width;
		$newHeight = ceil($newWidth * $ratio);

		// Re-sized image
		$convertedImage = imagecreatetruecolor($newWidth, $newHeight);

		switch ($sourceType)
		{
			case 'png':
				if (!function_exists('imagecreatefrompng'))
				{
					return false;
				}

				$src = @imagecreatefrompng($sourceFile);

				break;

			case 'gif':
				if (!function_exists('imagecreatefromgif'))
				{
					return false;
				}

				$src = @imagecreatefromgif($sourceFile);

				break;

			case 'bmp':
				if (!function_exists('imagecreatefrombmp'))
				{
					return false;
				}

				$src = @imagecreatefrombmp($sourceFile);
				break;

			case 'wbmp':
				if (!function_exists('imagecreatefromwbmp'))
				{
					return false;
				}

				$src = @imagecreatefromwbmp($sourceFile);
				break;

			case 'jpg':
				if (!function_exists('imagecreatefromjpeg'))
				{
					return false;
				}

				$src = @imagecreatefromjpeg($sourceFile);

				if ($cacheType != 'webp')
				{
					// Create progressive JPG
					imageinterlace($convertedImage, true);
				}

				break;

			case 'xbm':
				if (!function_exists('imagecreatefromxbm'))
				{
					return false;
				}

				$src = @imagecreatefromxbm($sourceFile);

				break;

			case 'xpm':
				if (!function_exists('imagecreatefromxpm'))
				{
					return false;
				}

				$src = @imagecreatefromxpm($sourceFile);

				break;

			// Hey, I have no idea what this file is!
			default:
				return false;
				break;
		}

		if (in_array($sourceType, ['png', 'gif']))
		{
			imagealphablending($convertedImage, false);
			imagesavealpha($convertedImage, true);
			$transparent = imagecolorallocatealpha($convertedImage, 255, 255, 255, 127);
			imagefilledrectangle($convertedImage, 0, 0, $newWidth, $newHeight, $transparent);
		}

		// Do the resize in memory
		imagecopyresampled($convertedImage, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
		imagedestroy($src);

		// This is just for debugging :)
		// $blackcolor = imagecolorallocate($convertedImage, 0, 0, 0);
		// imagestring($convertedImage, 5, 0, 0, sprintf('THIS IS THE %s %dpx IMAGE', $cacheType, $newWidth), $blackcolor);

		/**
		 * Sharpen the downscaled image
		 *
		 * @see https://github.com/MattWilcox/Adaptive-Images/blob/3cc26652a9be51e2c5fad36e4dc889b65ee45a36/adaptive-images.php#L179-L187
		 */
		if (($this->sharpen) && ($newWidth != $width) && (function_exists('imageconvolution')))
		{
			$intSharpness = $this->findSharp($width, $newWidth);

			$arrMatrix = [
				[
					-1,
					-2,
					-1,
				],
				[
					-2,
					$intSharpness + 12,
					-2,
				],
				[
					-1,
					-2,
					-1,
				],
			];

			imageconvolution($convertedImage, $arrMatrix, $intSharpness, 0);
		}

		$ret = false;

		switch ($cacheType)
		{
			case 'png':
				if (function_exists('imagepng'))
				{
					$ret = @imagepng($convertedImage, $cacheFile, max((int) ceil((100 - $this->quality) / 10), 9));
				}

				break;

			case 'gif':
				if (function_exists('imagegif'))
				{
					$ret = @imagegif($convertedImage, $cacheFile);
				}

				break;
			case 'bmp':
				if (function_exists('imagebmp'))
				{
					$ret = @imagebmp($convertedImage, $cacheFile);
				}

				break;
			case 'wbmp':
				if (function_exists('imagewbmp'))
				{
					$ret = @imagewbmp($convertedImage, $cacheFile);
				}

				break;
			case 'jpg':
				if (function_exists('imagejpeg'))
				{
					$ret = @imagejpeg($convertedImage, $cacheFile, $this->quality);
				}

				break;
			case 'xbm':
				if (function_exists('imagexbm'))
				{
					$ret = @imagexbm($convertedImage, $cacheFile);
				}

				break;
			case 'webp':
				if (function_exists('imagewebp'))
				{
					$ret = @imagewebp($convertedImage, $cacheFile, $this->quality);
				}

				break;
		}

		imagedestroy($convertedImage);

		return $ret;
	}

	/**
	 * Sharpen images function.
	 *
	 * @param   int  $intOrig
	 * @param   int  $intFinal
	 *
	 * @return  int
	 * @since   1.0.0
	 *
	 * @see     https://github.com/MattWilcox/Adaptive-Images/blob/master/adaptive-images.php#L109
	 */
	private function findSharp($intOrig, $intFinal)
	{
		$intFinal = $intFinal * (750.0 / $intOrig);
		$intA     = 52;
		$intB     = -0.27810650887573124;
		$intC     = .00047337278106508946;
		$intRes   = $intA + $intB * $intFinal + $intC * $intFinal * $intFinal;

		return max(round($intRes), 0);
	}
}