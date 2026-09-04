<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image\Tests;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait TempDirAssertions
{
    /**
     * macOS reports its temp directory unresolved but creates files in the
     * resolved one, so both sides have to be normalized before comparing.
     */
    protected function assertInTempDir(\SplFileInfo $file): void
    {
        $this->assertSame(\realpath(\sys_get_temp_dir()), \realpath(\dirname($file)));
    }
}
