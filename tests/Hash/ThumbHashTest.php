<?php

/*
 * This file is part of the zenstruck/image package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Image\Tests\Hash;

use PHPUnit\Framework\TestCase;
use Zenstruck\Image\Hash\ThumbHash;
use Zenstruck\ImageFileInfo;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ThumbHashTest extends TestCase
{
    /**
     * @test
     */
    public function generate_from_image(): void
    {
        $fixtureDir = __DIR__.'/../Fixture/files';

        $thumbHash = ImageFileInfo::wrap($fixtureDir.'/symfony.jpg')->thumbHash();

        $this->assertStringStartsWith('data:image/png;base64,', $thumbHash->dataUri());
        $this->assertGreaterThan(20, \mb_strlen($thumbHash->key()));
        $this->assertGreaterThan(20, \count($thumbHash->hash()));
        $this->assertGreaterThan(0.79, $thumbHash->approximateAspectRatio());
        $this->assertLessThan(0.9, $thumbHash->approximateAspectRatio());
    }

    /**
     * @test
     */
    public function generate_from_key_string(): void
    {
        $thumbHash = ThumbHash::from('JAgSBgD3xhinqMd3WXuLhZmoAAAAAAA');

        $this->assertStringStartsWith('data:image/png;base64,', $thumbHash->dataUri());
        $this->assertGreaterThan(20, \mb_strlen($thumbHash->key()));
        $this->assertGreaterThan(20, \count($thumbHash->hash()));
        $this->assertGreaterThan(0.79, $thumbHash->approximateAspectRatio());
        $this->assertLessThan(0.9, $thumbHash->approximateAspectRatio());

        $this->assertSame('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABsAAAAgCAYAAADjaQM7AAAORklEQVR4AQBtAJL/AP////////////////359//Rzcv/p6Oh/4J+e/9lYV//U09N/0pGRP9JRUP/TEhG/1JOTP9WUlD/WlZU/1xYVv9eWlj/Y19d/25qaP+AfHr/mpaU/7u3tf/h3dv//////////////////////wBtAJL/AP///////////////+7q6P/Dv73/mpaU/3ZycP9bV1X/SkZE/0M/Pf9DPz3/R0NB/01JRv9RTUv/VFBO/1VRT/9XU1H/W1dV/2RgXv91cW//j4uJ/6+rqf/V0c//+/f1/////////////////wBtAJL/AP//////////+fXz/9TQzv+rp6X/g399/2JeXP9KRkT/PDg2/zczMf85NTP/Pjo4/0VBP/9JRUP/S0dF/0tHRf9LR0X/TUlH/1RQTv9jX13/e3d1/5uXlf/Au7n/5eHf/////////////////wBtAJL/AP/9+//y7uz/19PR/7Swrv+NiYf/aWVj/0tHRf82MjD/LCgm/yomJP8vKyn/NzMx/z46OP9CPjz/Qz89/0E9O/8+Ojj/PTk3/0I+PP9OSkj/ZGBe/4J+fP+moqD/zMfF/+7q6P///////////wBtAJL/AN/b2f/Rzcv/t7Ox/5aSkP9ybmz/UU1L/zczMf8mIiD/IBwa/yIeHP8qJiT/NDAu/zw4Nv9APDr/Pzs5/zs3Nf80MC7/MCwq/zEtK/87NzX/T0pI/2tnZf+Oioj/s6+t/9XRz//v6+n//fn3/wBtAJL/AMTAvv+3s7D/npqY/396eP9dWVf/QDw6/yomJP8eGhf/HBgW/yIeHP8tKSf/OTUz/0I+PP9FQT//Qz89/zs3Nf8xLSv/KSUj/ycjIf8tKSf/Pjo4/1lVU/97d3X/oJyZ/8K+vP/c2Nb/6ubk/wBtAJL/ALSwrv+no6H/j4uJ/3Jta/9TT03/ODQy/yYiIP8eGhj/IBwa/yomJP84NDL/RkJA/09LSf9STkz/TUlH/0I+PP80MC7/KCQi/yIeHP8mIiD/NTEv/05KSP9va2n/lJCO/7ezsf/Rzcv/4Nza/wBtAJL/AK+qqP+inpz/i4eF/25qaP9RTUv/OTUz/yomJP8lIR//Kycl/zg0Mv9JRUP/WFRS/2FdW/9jX13/XFhW/05KR/88ODb/LCgm/yMfHf8kIB7/MS0r/0pGRP9rZ2X/kIyK/7Ovrf/Pysj/3dnX/wBtAJL/ALGtq/+loZ//joqI/3JubP9XUlD/QDw6/zMvLf8wLCr/OTUz/0hEQv9aVlT/amZk/3Rwbv90cG7/amZk/1lVU/9EQD7/MS0r/yUhH/8kIB7/MCwq/0hEQv9qZmT/kIyK/7Swrv/Rzcv/4Nza/wBtAJL/ALi0sv+sp6X/lZGP/3l1c/9dWVf/R0NB/zs3Nf86NjT/REA+/1VRT/9oZGL/eHRy/4F9e/9/e3n/dHBt/2BcWv9IREL/My8t/yUhH/8jHhz/Lioo/0dDQf9qZmT/kY2L/7ezsf/U0M7/5ODe/wBtAJL/AL66uP+xrav/mpaU/315d/9hXVv/S0dF/z87Of8+Ojj/SUVD/1pWVP9taWf/fXl3/4WBf/+Cfnv/dHBu/15aWP9EQD7/LSkn/x4aGP8cGBb/KCQi/0I+PP9mYmD/j4uJ/7eysP/V0c//5uLg/wBtAJL/AMC8uv+zr63/m5eV/315d/9gXFr/SUVD/zw4Nv87NzX/REA+/1VRT/9oZGL/d3Nx/315d/95dXP/aWVj/1JOTP83MzH/HxsZ/xAMCv8PCwn/HBgW/zg0Mv9eWlj/iYWD/7KurP/Szsz/5ODe/wBtAJL/AL66uP+wrKr/l5OR/3h0cv9ZVVP/QDw6/zIuLP8wLCr/ODQy/0hEQv9ZVVP/Z2Nh/2xoZv9nY2H/VlJQ/z46OP8jHx3/CwcF/wAAAP8AAAD/DAgG/yomJP9TT03/gHx6/6unpf/MyMb/3trY/wBtAJL/ALq2tP+rp6X/kY2L/3Fta/9QTEr/NjIw/yUhH/8hHRv/KCQi/zYyMP9GQkD/U05M/1dSUP9QTEr/Pzs5/yciIP8MCAb/AAAA/wAAAP8AAAD/AAAA/x0ZF/9IREL/d3Nx/6Ofnf/GwsD/2NTS/wBtAJL/ALezsf+opKL/jYmH/2tnZf9JRUP/LSkn/xsXFf8VEQ//GhYU/yYiIP80MC7/Pzs5/0I+PP87NzX/KiYk/xIODP8AAAD/AAAA/wAAAP8AAAD/AAAA/xYRD/9CPjz/c29t/6Ccmv/Dv73/1tLQ/wBtAJL/ALq2tP+rp6X/j4uJ/2xoZv9JRUP/Kycl/xcTEf8QDAr/Ew8N/x0ZF/8qJiT/My8t/zUxL/8tKSf/HRkX/wYCAP8AAAD/AAAA/wAAAP8AAAD/AAAA/xgUEv9GQkD/d3Nx/6Whn//IxML/29fV/wBtAJL/AMXBv/+2srD/mpaU/3ZycP9STkz/NDAu/x8bGf8WEhD/FxMR/yAcGv8rJyX/My8t/zQwLv8tKSf/HRkX/wgEAv8AAAD/AAAA/wAAAP8AAAD/AQAA/ycjIf9WUlD/h4OB/7Swrv/X09H/6ubk/wBtAJL/ANnV0//KxsT/rqqo/4uHhf9nY2H/SERC/zMvLf8pJSP/KSUj/zAsKv86NjT/QTw6/0E9O/86NjT/Kycl/xgUEv8GAgD/AAAA/wAAAP8DAAD/HRgW/0M/Pf9xbWv/op6c/87KyP/v6+n///37/wBtAJL/APTw7v/l4d//ysbE/6ikov+FgX//ZmJg/1FNS/9HQkD/RkJA/0xIRv9UUE7/WlZU/1pWVP9TT03/RUE//zQwLv8kIB7/GhYU/xsXFf8oJCL/Qj48/2hkYv+VkY//xMC+/+7q6P///////////wBtAJL/AP///////vz/6eXi/8jEwv+no6D/ioaE/3Vxb/9rZ2X/aWVj/25qaP91cW//enZ0/3p2dP9zb23/Z2Nh/1hTUf9KRkT/Qj48/0VBP/9TT03/bWln/5KOjP+8uLb/6OTi/////////////////wBtAJL/AP///////////////+bi4P/Hw8H/rKim/5mVk/+Pi4n/joqI/5KOjP+YlJL/nJiW/5uXlf+UkI7/iYWD/3x4dv9wbGr/a2dl/29raf99eXf/l5OR/7m1s//h3dv//////////////////////wBtAJL/AP////////////////76+P/i3tz/ycXD/7i0sv+vq6n/rqqo/7Gtq/+2srD/ubWz/7izsf+xrav/pqKg/5uXlf+RjYv/joqI/5OOjP+hnZv/ubWz/9nV0//8+Pb//////////////////////wBtAJL/AP/////////////////////y7uz/3dnX/87KyP/GwsD/xcG//8jEwv/MyMb/zsrI/8vHxf/Fwb//u7e1/7Gtq/+ppaP/p6Oh/6yopv+7t7X/0c3L/+3p5////////////////////////////wBtAJL/AP/////////////////////49PL/5uLg/9rV0//Tz83/08/M/9XRz//Y09H/2NTS/9XRz//Oysj/xcG//7y4tv+2sq//tbGv/7u3tf/JxcP/3dnX//by8P///////////////////////////wBtAJL/AP/////////////////////18e//5uLg/9zY1v/X09H/19PR/9nV0//a1tT/2dXT/9XRz//Oysj/xcG//725t/+5tbP/ubWz/8C8uv/Nycf/39vZ//Xx7////////////////////////////wBtAJL/AP////////////////v39f/t6ef/4d3b/9nV0//V0c//1dHP/9bS0P/X0tD/1NDO/8/Lyf/IxML/wLu5/7m1s/+1sa//t7Ox/766uP/Lx8X/29fV/+3p5///+/n//////////////////////wBtAJL/AP////////3/+vXz/+7q6P/i3tz/2dXT/9PPzf/Rzcv/0c3L/9HNy//QzMr/zcnH/8fDwf+/u7n/t7Ox/7Gtq/+vq6n/sq6s/7q2tP/GwsD/1dHP/+Tg3v/z7uz///v5/////////////////wBtAJL/APfz8f/z7uz/6+fl/+Hd2//Z1dP/0s7M/87KyP/Nycf/zcnH/83Jx//Lx8X/xsLA/7+7uf+3s7H/r6up/6qmpP+ppaP/ramn/7aysP/Cvrz/z8vJ/9zY1v/o5OL/8Ozq//by8P/69vT/+/f1/wBtAJL/AOrl4//m4uD/4Nza/9nV0//Szsz/zsrI/8vHxf/Lx8X/y8fF/8rGxP/Hw8H/wr68/7q2tP+yrqz/qqak/6aioP+moqD/q6el/7Swrv/AvLr/zcnH/9jU0v/h3dv/5+Ph/+rm5P/r5+X/6+fl/wBtAJL/AOHd2//e2tj/2tXT/9TQzv/Py8n/zMjG/8vHxf/Lx8X/y8fF/8rGxP/GwsD/wLy6/7i0sv+vq6n/qKSi/6Sgnv+mop//q6el/7Wxr//Bvbv/zcnH/9fT0f/e2tj/4t7c/+Pf3f/i3tz/4t7c/wBtAJL/AN3Y1v/a1tT/1tLQ/9LOzP/Py8n/zcnH/8zIxv/Nycf/zcnH/8vHxf/Hw8H/wLy6/7ezsf+vq6n/qKSi/6Whn/+moqD/ramn/7ezsf/Dv73/z8vJ/9jU0v/e2tf/4Nza/+Dc2f/e2tj/3dnX/wFtAJL/ANvX1f/Z1dP/1dHP/9LOzP/Py8n/zcnH/83Jx//Oysj/zsrI/8zIxv/IxML/wb27/7i0sv+vq6n/qKSi/6Whn/+no6H/rqqo/7m1s//Fwb//0MzK/9nV0//e2tj/39vZ/9/a2P/d2df/29fV/3HW/YApbA/VAAAAAElFTkSuQmCC', $thumbHash->dataUri());
        $this->assertEqualsWithDelta(0.86, $thumbHash->approximateAspectRatio(), 0.01);
        $this->assertSame('JAgSBgD3xhinqMd3WXuLhZmoAAAAAAA', $thumbHash->key());
        $this->assertCount(23, $thumbHash->hash());
    }
}
