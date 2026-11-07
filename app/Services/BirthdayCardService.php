<?php

namespace App\Services;

use App\Models\User;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BirthdayCardService
{
    public const DEFAULT_MESSAGE = 'May God continue to bless you with good health, joy, wisdom and greater impact in His Kingdom.';

    public function render(User $user, string $message): string
    {
        $template = imagecreatefrompng(public_path('assets/images/templates/birthday-card-template.png'));

        if ($template === false) {
            throw new RuntimeException('Birthday card template could not be loaded.');
        }

        $avatar = imagecreatefromstring($this->avatarBinary($user));

        if ($avatar === false) {
            imagedestroy($template);
            throw new RuntimeException('Member profile image could not be loaded.');
        }

        $this->overlayChurchLogo($template);

        // Crop from the image centre before fitting it into the circular frame,
        // so portraits and landscape profile photos always keep their subject centred.
        $photo = $this->circularAvatar($avatar, 432);
        imagecopy($template, $photo, 62, 342, 0, 0, 432, 432);

        $blue = imagecolorallocate($template, 6, 43, 111);
        $white = imagecolorallocate($template, 255, 255, 255);
        $gold = imagecolorallocate($template, 197, 123, 0);
        $this->roundedRectangle($template, 590, 614, 1166, 706, 42, $blue);
        $this->centeredText($template, $user->name, 42, 878, 678, $white, 540);

        $this->roundedRectangle($template, 897, 782, 1007, 829, 12, $gold);
        $this->roundedRectangle($template, 1041, 782, 1149, 829, 12, $gold);
        $this->centeredText($template, $user->birthday->format('M'), 31, 952, 817, imagecolorallocate($template, 0, 0, 0), 92);
        $this->centeredText($template, $user->birthday->format('d'), 31, 1095, 817, imagecolorallocate($template, 0, 0, 0), 92);

        if ($message !== self::DEFAULT_MESSAGE) {
            imagefilledrectangle($template, 540, 889, 1186, 1038, imagecolorallocate($template, 255, 255, 255));
            $this->multilineCenteredText($template, $message, 27, 864, 910, imagecolorallocate($template, 7, 43, 108), 590, 37);
        }

        ob_start();
        imagejpeg($template, null, 92);
        $binary = (string) ob_get_clean();

        imagedestroy($avatar);
        imagedestroy($photo);
        imagedestroy($template);

        return $binary;
    }

    private function overlayChurchLogo(\GdImage $card): void
    {
        $logo = imagecreatefrompng(public_path('assets/images/resources/rccg-official-logo.png'));

        if ($logo === false) {
            throw new RuntimeException('Official RCCG logo could not be loaded.');
        }

        $size = 126;
        $resized = imagecreatetruecolor($size, $size);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagefilledrectangle($resized, 0, 0, $size, $size, imagecolorallocatealpha($resized, 0, 0, 0, 127));
        imagecopyresampled($resized, $logo, 0, 0, 0, 0, $size, $size, imagesx($logo), imagesy($logo));

        // Give the supplied transparent logo a clean white background and remove the old emblem.
        imagefilledellipse($card, 110, 105, 140, 140, imagecolorallocate($card, 255, 255, 255));
        imagecopy($card, $resized, 47, 42, 0, 0, $size, $size);

        imagedestroy($logo);
        imagedestroy($resized);
    }

    private function avatarBinary(User $user): string
    {
        $storagePath = MediaUrl::toStoragePath($user->avatar);

        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            return Storage::disk('public')->get($storagePath);
        }

        if (filled($user->avatar) && MediaUrl::isAbsolute($user->avatar)) {
            $response = Http::timeout(15)->get($user->avatar);

            if ($response->successful()) {
                return $response->body();
            }
        }

        $fallback = file_get_contents(public_path('default-avatar-human.png'));

        if ($fallback === false) {
            throw new RuntimeException('Default profile image could not be loaded.');
        }

        return $fallback;
    }

    private function circularAvatar(\GdImage $source, int $diameter): \GdImage
    {
        $canvas = imagecreatetruecolor($diameter, $diameter);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefilledrectangle($canvas, 0, 0, $diameter, $diameter, imagecolorallocatealpha($canvas, 0, 0, 0, 127));

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $side = min($sourceWidth, $sourceHeight);
        imagecopyresampled($canvas, $source, 0, 0, (int) (($sourceWidth - $side) / 2), (int) (($sourceHeight - $side) / 2), $diameter, $diameter, $side, $side);

        $radius = $diameter / 2;
        for ($y = 0; $y < $diameter; $y++) {
            for ($x = 0; $x < $diameter; $x++) {
                if ((($x - $radius) ** 2) + (($y - $radius) ** 2) > ($radius ** 2)) {
                    imagesetpixel($canvas, $x, $y, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
                }
            }
        }

        imagealphablending($canvas, true);

        return $canvas;
    }

    private function roundedRectangle(\GdImage $image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
    {
        imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
        imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    }

    private function centeredText(\GdImage $image, string $text, int $size, int $centerX, int $baseline, int $color, int $maxWidth): void
    {
        $font = public_path('assets/fonts/Arial.ttf');
        while ($size > 18 && $this->textWidth($text, $size, $font) > $maxWidth) {
            $size--;
        }

        imagettftext($image, $size, 0, (int) ($centerX - ($this->textWidth($text, $size, $font) / 2)), $baseline, $color, $font, $text);
    }

    private function multilineCenteredText(\GdImage $image, string $text, int $size, int $centerX, int $top, int $color, int $maxWidth, int $lineHeight): void
    {
        $font = public_path('assets/fonts/Arial.ttf');
        $lines = $this->wrapText($text, $size, $font, $maxWidth);
        $baseline = $top + $size;

        foreach (array_slice($lines, 0, 3) as $line) {
            $this->centeredText($image, $line, $size, $centerX, $baseline, $color, $maxWidth);
            $baseline += $lineHeight;
        }
    }

    private function wrapText(string $text, int $size, string $font, int $maxWidth): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $line = '';

        foreach ($words as $word) {
            $candidate = trim($line.' '.$word);
            if ($line !== '' && $this->textWidth($candidate, $size, $font) > $maxWidth) {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $candidate;
            }
        }

        if ($line !== '') {
            $lines[] = $line;
        }

        return $lines;
    }

    private function textWidth(string $text, int $size, string $font): int
    {
        $box = imagettfbbox($size, 0, $font, $text);

        return abs($box[2] - $box[0]);
    }
}
