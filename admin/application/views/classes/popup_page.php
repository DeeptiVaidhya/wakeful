<div class="row">
    <div class="col-md-12">
        <?php
        $content = '';
        foreach ($master as $key => $value) {
            if (isset($result[$key])) {
                $str = $result[$key];
                $file = ($key == 'files_id') ? get_file($result[$key]) : '';
                if ($file) {
                    $str = $file;
                }
                $content .= '<div class="col-xs-12 col-md-4"><h5>' . $value . ':</h5></div>
                            <div class="col-xs-12 col-md-8"><h5 class="c-black title">' . $str . '</h5></div>';
            }
        }
        if (isset($result['sub_details']) && !empty($result['sub_details'])) {
            $content .= '<div class="col-xs-12"><h4>Details:</h4></div>';
            $content .= '<div class="col-xs-12"><table class="table table-striped table-bordered">';
            $str = "";
            $thArr = array();
            foreach ($result['sub_details'] as $index => $v) {
                $str .= "<tr>";
                foreach ($sub_details as $k => $val) {
                    if (isset($v[$k])) {
                        $text = $v[$k];
                        $file = ($k == 'files_id') ? get_file($v[$k]) : '';
                        if ($file) {
                            $text = '<div class="w-100px">'.$file.'</div>';
                        }
                        $str .= '<td class="c-black title">' . $text . '</td>';
                        if (!$index) {
                            $thArr[] = "<th>" . $val . "</th>";
                        }
                    }
                }
                $str .= "</tr>";
            }
            $content .= "<tr>" . implode("", $thArr) . "</tr>";
            $content .= $str;
            $content .= "</table></div>";
        }
        echo $content;
        ?>   
    </div>
</div>

