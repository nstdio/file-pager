<?php
/**
 * Class MockFileGenerator
 *
 * @author Edgar Asatryan <nstdio@gmail.com>
 */
class MockFile
{
    public static $content = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis porttitor sapien. Sed dapibus eros lacus, nec sagittis lacus molestie a. Duis cursus tempor urna, non tincidunt erat tempus quis. Morbi ac molestie eros. Cras sagittis aliquet nulla, sit amet volutpat orci laoreet quis. Aliquam non erat nisi. Donec sollicitudin quam quis purus fringilla, vitae rhoncus metus interdum. In non est sed augue imperdiet consectetur. Nam efficitur dui ac magna faucibus lobortis. Mauris sed orci malesuada, maximus sapien non, blandit ligula. Ut in vestibulum eros. Duis massa massa, varius et sollicitudin id, volutpat vitae tellus. Curabitur turpis ligula, accumsan in turpis eget, egestas tempor justo. Aenean vitae venenatis neque, quis ultricies metus. Fusce est orci, scelerisque id interdum eu, volutpat non tellus. Etiam blandit ipsum eget molestie pellentesque. Nullam lobortis pellentesque nibh, non placerat dui semper eu. Nam venenatis, arcu a placerat aliquam, dui magna egestas felis, tincidunt convallis massa dolor a justo. Fusce ornare varius libero sed accumsan. Cras lacinia, velit in maximus blandit, mauris purus dictum urna, a tincidunt nunc purus commodo velit. Sed a nisl facilisis, dictum velit vel, imperdiet velit. In augue neque, gravida vel luctus ac, egestas et urna. Sed ullamcorper metus ut euismod varius. Praesent volutpat sapien semper sapien suscipit egestas. Phasellus congue metus eu facilisis pulvinar. Cras suscipit et ipsum laoreet fermentum. Vestibulum nec condimentum massa. Pellentesque tincidunt erat vel purus tincidunt, vel elementum nisi mollis. Donec nisi nisl, blandit at leo vel, mattis vestibulum mauris. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aliquam condimentum libero sed placerat porta. Phasellus id porta est. Suspendisse blandit mattis erat, sit amet consectetur orci malesuada sed. Mauris laoreet neque vel quam fringilla, et congue purus finibus. Cras luctus tempor leo, at maximus nibh pharetra porta. In posuere erat sapien, nec pellentesque ligula accumsan sed. Fusce congue molestie varius. Duis velit risus, lobortis sed eros at, consectetur tristique massa. Maecenas elementum molestie metus, eget posuere lectus viverra quis. Nunc aliquam, lectus eget tincidunt aliquet, dolor odio porta sem, sed molestie nisl nunc eu lorem. Donec ac porta metus. Aenean ullamcorper ipsum non consequat vulputate. Maecenas quis dolor ut lectus pharetra faucibus ut nec libero. Duis ligula ex, scelerisque et mattis quis, ornare ac enim. Etiam sagittis et ex at dignissim. Pellentesque dignissim cursus nunc, maximus iaculis ligula. Fusce ac lobortis magna. Duis egestas semper sapien nec consectetur. Vestibulum id ligula quis enim rutrum consectetur. Vivamus at eleifend mi. Morbi arcu nulla, tempus non erat eu, rhoncus tempus mi. Vivamus sit amet diam a quam euismod commodo. Pellentesque bibendum, est sit amet ullamcorper lobortis, tortor orci tristique libero, quis tempor purus massa ut nisi. Nam at sapien tempus, elementum nunc at, semper tortor. Morbi velit sem, faucibus sed viverra a, pretium at velit.";

    public static function create($file, $lines)
    {
        $handler = fopen($file, 'w');

        if ($handler !== false) {
            for ($i = 0; $i < $lines; $i++) {
                if ($i === $lines - 1) {
                    fwrite($handler, self::$content);
                } else {
                    fwrite($handler, self::$content . "\n");
                }
            }
            fclose($handler);
        }
    }
}