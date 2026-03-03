<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "to_inventory");

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$current_user = $_SESSION['username'];

// 1. Fetch messages from database
$query = "SELECT m.*, u.username as sender_name 
          FROM messages m 
          JOIN users u ON m.sender_id = u.id 
          WHERE m.receiver_id = (SELECT id FROM users WHERE username = ?)
          ORDER BY m.created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $current_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inbox</title>
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        /* Your existing CSS here... */
        body { margin-top:20px; background:#eee; }
        .email { padding: 20px 10px 15px 10px; font-size: 1em; }
        .grid { position: relative; width: 100%; background: #fff; border-radius: 2px; margin-bottom: 25px; box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.1); }
        .grid .grid-body { padding: 15px 20px; }
        .unread { background-color: #f4f8ff !important; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
<div class="row">
    <div class="col-md-12">
        <div class="grid email">
            <div class="grid-body">
                <div class="row">
                    <div class="col-md-3">
                        <h2 class="grid-title"><i class="fa fa-inbox"></i> Inbox</h2>
                        <button class="btn btn-block btn-primary" data-toggle="modal" data-target="#compose-modal">
                            <i class="fa fa-pencil"></i>&nbsp;&nbsp;NEW MESSAGE
                        </button>
                        <hr>
                        <ul class="nav nav-pills nav-stacked">
                            <li class="header">Folders</li>
                            <li class="active"><a href="#"><i class="fa fa-inbox"></i> Inbox (<?php echo $count; ?>)</a></li>
                            <li><a href="#"><i class="fa fa-mail-forward"></i> Sent</a></li>
                        </ul>
                    </div>

                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <?php if($count > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                                            <tr class="<?php echo $row['is_read'] ? 'read' : 'unread'; ?>">
                                                <td class="action"><input type="checkbox" /></td>
                                                <td class="action"><i class="fa fa-star-o"></i></td>
                                                <td class="name"><?php echo htmlspecialchars($row['sender_name']); ?></td>
                                                <td class="subject"><?php echo htmlspecialchars($row['subject']); ?></td>
                                                <td class="time"><?php echo date('h:i A', strtotime($row['created_at'])); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center">No messages found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="compose-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">New Message</h4>
            </div>
            <form action="send_message.php" method="POST">
                <div class="modal-body">
                    <input type="text" name="to_user" class="form-control" placeholder="Recipient Username" required><br>
                    <input type="text" name="subject" class="form-control" placeholder="Subject"><br>
                    <textarea name="message" class="form-control" style="height:150px" placeholder="Message"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Discard</button>
                    <button type="submit" name="send_btn" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

</body>
</html>