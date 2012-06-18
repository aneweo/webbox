<?php
if($dirok // Show buttons if the current dir is not empty and it's not at the home page
    || request_okay($_GET, 'public-box')) { // or if the user is requesting a public box
    if ($isPublic || request_okay($_GET, 'public-box') || (request_okay($_GET, 'key') && $dirok)) {
        // Download button -->
        print('<button class="btn btn-primary" type="submit" name="download" value="download">
            <i class="icon-download-alt icon-white"></i> Download
            </button>' . "\n");
    } else if($isPrivate) { // only show "add priviledge" button in private folder
        print('<a class="btn btn-info" data-toggle="modal"
            onClick="if (check(\'checkbox-form\') == false) $(\'#privilege\').modal(\'show\'); return false;"
            type="submit" name="grant" value="grant" href="#privilege">
            <i class="icon-user icon-white"></i> Modify privilege
            </a>' . "\n");
    }
    if($_SESSION['user'] && !request_okay($_GET, 'public-box')) { // Only authorized user can delete
        // Delete button
        print('<button class="btn btn-danger" type="submit" name="delete" value="delete"
            onClick="if(check(\'checkbox-form\'))
                    return confirm(\'Are you sure you want to delete the selected items?\nThis cannot be undone.\')">
            <i class="icon-trash icon-white"></i> Delete</button>' . "\n");
    }
    if ($isP) { //Popup ?>
        <div class="modal hide" id="privilege">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Modify Access</h3>
            </div>
            <div class="modal-body">
                <p><strong>Grant Access:</strong></p>
                <label><p>Enter email addresses to grant file access to
                        <br><small><i>Please use commas to separate multiple emails</i></small></p></label>
                <textarea type="text" class="input-xlarge" rows="3" name="granted-users" placeholder="user@gmail.com, user2@gmail.com"></textarea>
                <br><br>
                <p><strong>Remove Access:</strong></p>
                <label><p>Enter email addresses to remove file access from
                        <br><small><i>Please use commas to separate multiple emails</i></small></p></label>
                <textarea type="text" class="input-xlarge" rows="3" name="removed-users" placeholder="user@gmail.com, user2@gmail.com"></textarea>
                <br>
                <div class="modal-footer">
                    <a href="#" class="btn" data-dismiss="modal">Close</a>
                    <button name="access" value="access" type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    <?php }
}
