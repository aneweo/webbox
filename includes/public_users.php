<?php
$public_box = $_GET['public-box'];
if (!empty($dirs) || !empty($files)) { // if dir is not empty?>
    <script type="text/javascript" src="bootstrap/js/form.helper.js"></script>
    <script type="text/javascript" src="bootstrap/js/sorttable.js"></script>
    <form name="checkbox-form" action="actions.php?dir=<?php echo $current_dir; ?>" method="post"
        onKeyPress="return disableEnterKey(event)">
        <!-- Table listing -->
        <table class="tbl table table-bordered sortable">
            <tr>
                <?php if($dirok || $isPublic) {?>
                <th class="sorttable_nosort" width="70px;">
                    <a href="javascript:selectToggle(true, 'checkbox-form');">All</a> /
                    <a href="javascript:selectToggle(false, 'checkbox-form');">None</a>
                </th>
                <?php } ?>
                <th>Name</th>
                <th>Size</th>
                <th>Last modified</th>
            </tr>
            <!-- Folders -->
            <?php
            $arsize = sizeof($dirs);
            for($i=0;$i<$arsize;$i++) {
                $folder_path = $includeurl . $leadon . $dirs[$i];
            ?>
            <tr>
                <td></td>
                <td>
                    <img src="<?php echo $includeurl; ?>dlf/folder.png" alt="<?php echo $dirs[$i];?>" />
                    <a href="<?php echo strip_tags($_SERVER['PHP_SELF']).'?dir='.urlencode($dirs[$i]) .
                        "&public-box=$public_box";?>">
                        <strong><?php echo $dirs[$i];?></strong>
                    </a>
                </td>
                <td>
                    <em><?php echo format_bytes(filesize($includeurl.$leadon.$files[$i]));?></em>
                </td>
                <td>
                    <?php echo date ("M d Y h:i:s A", filemtime($includeurl.$leadon.$dirs[$i]));?>
                </td>
            </tr>
            <?php
            }
            //<!-- /Folders -->
            //<!-- /Files -->
            $arsize = sizeof($files);
            for($i=0;$i<$arsize;$i++) {
                $icon = 'unknown.png';
                $ext = strtolower(substr($files[$i], strrpos($files[$i], '.')+1));

                if($filetypes[$ext]) {
                    $icon = $filetypes[$ext];
                }

                $filename = $files[$i];
                if(strlen($filename)>43) {
                    $filename = substr($files[$i], 0, 40) . '...';
                }

                $fileurl = $includeurl . $leadon . $files[$i];
            ?>
            <tr>
                <td><input type="checkbox" name="checked_items[]" value="<?php echo $fileurl; ?>"></td>
                <td>
                    <img src="<?php echo $includeurl; ?>dlf/<?php echo $icon;?>" alt="<?php echo $files[$i];?>" />
                    <a href="<?php echo $fileurl;?>">
                        <strong><?php echo $filename;?></strong>
                    </a>
                    <td>
                        <em><?php echo format_bytes(filesize($includeurl.$leadon.$files[$i]));?></em>
                    </td>
                    <td>
                        <?php echo date ("M d Y h:i:s A", filemtime($includeurl.$leadon.$files[$i]));?>
                    </td>
                </tr>
            <?php
            } //<!-- /Files -->
            ?>
        </table>
        <!-- /Table listing -->
        <?php include_once('includes/buttons.php'); // Buttons?>
    </form>
<?php } // if not empty
