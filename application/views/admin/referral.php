<script src="<?php echo asset_url('assets/js/referral.js'); ?>"></script>
<script src="<?php echo asset_url('assets/js/common.js'); ?>"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">


<style>
    .editable {
        display: none;
    }

    .btn-primary {
        background-color: #0d6efd !important;
    }

    .btn-danger {
        background-color: #dc3545 !important;
    }

    .btn-dark {
        background-color: #dc3545 !important;
    }

    .btn {
        background: none;
    }

    #dateTimeRange {
        width: 235px;
        font-size: 10px;
    }

    .userList {
        width: 150px;
    }
    .userList:disabled,
    .userList[disabled] {
        border: none;
        color: black;
    }
</style>

<div class="container py-3">
    <h2 class="border-bottom border-dark">Referral System</h2>
    <div class="row">
        <div class="col-12">
            <!-- Table Form start -->
            <form action="" id="form-data">
                <input type="hidden" name="id" value="">
                <table class='table table-hovered table-stripped table-bordered' id="form-tbl">
                    <thead>
                        <tr>
                            <th class="text-center p-1">#</th>
                            <th class="text-center p-1">Referrer Name</th>
                            <th class="text-center p-1">Referrer Credits ($)</th>
                            <th class="text-center p-1">Referral Code</th>
                            <th class="text-center p-1">Referral Link</th>
                            <th class="text-center p-1">Referral Credits ($)</th>
                            <th class="text-center p-1">Cap Amount</th>
                            <th class="text-center p-1">Total Redeemed</th>
                            <th class="text-center p-1">Start and End Date</th>
                            <th class="text-center p-1">Active</th>
                            <th class="text-center p-1">Created Date</th>
                            <th class="text-center p-1">Modified Date</th>
                            <th class="text-center p-1">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($referralData as $row) :
                        ?>
                            <tr data-id='<?php echo $row['id'] ?>'>
                                <td name="id"><?php echo $row['id'] ?></td>
                                <td name="referrer_name">
                                    <select id="search" placeholder="Choose user.." autocomplete="true" class="userList" disabled>
                                        <?php foreach ($users as $user) { ?>
                                            <option value="<?php echo $user['user_id']; ?>" <?php echo ($user['user_id']==  $row['referrer_id']) ? ' selected="selected"' : '';?>><?php echo $user['fullname']; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td name="referrer_value"><?php echo $row['referrer_value'] ?></td>
                                <td name="name" id="name"><?php echo $row['name'] ?></td>
                                <td name="link" id="link"><?php echo $row['link'] ?></td>
                                <td name="value"><?php echo $row['value'] ?></td>
                                <td name="cap_number"><?php echo $row['cap_number'] ?></td>
                                <td name="redemption_total"><?php echo $row['redemption_total'] ?></td>
                                <td name="referralDateTimeRange">
                                    <input type="text" id="dateTimeRange" name="dateTimeRange" placeholder="Choose dates" data-date='<?php echo ($row['referralDateTimeRange'] > 0) ? $row['referralDateTimeRange'] : '' ?>' class="form-control-sm" required disabled value="<?php echo ($row['referralDateTimeRange'] > 0) ? $row['referralDateTimeRange'] : '' ?>" />
                                </td>
                                <td name="status"><input type="checkbox" name="active" <?php echo ($row['status']==1 ? 'checked' : '');?> disabled></td>
                                <td name="created_date"><?php echo $row['created_date'] ?></td>
                                <td name="modified_date"><?php echo $row['modified_date'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm rounded-0 py-0 edit_data noneditable" type="button">Edit</button>
                                    <button class="btn btn-danger btn-sm rounded-0 py-0 delete_data noneditable mt-1" type="button">Delete</button>
                                    <button class="btn btn-sm btn-primary btn-flat rounded-0 px-2 py-0 editable">Save</button>
                                    <button class="btn btn-sm btn-dark btn-flat rounded-0 px-2 py-0 mt-1 editable" onclick="cancel_button($(this))" type="button">Cancel</button>
                                </td>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
            <!-- Table Form end -->
        </div>
        <div class="w-100 d-flex pposition-relative justify-content-center">
            <button class="btn btn-flat btn-primary" id="add_referral" type="button">Add New Referral</button>
        </div>
    </div>
</div>

<script>
    $(document).ready( function () {
        $('#form-tbl').DataTable();
    });
</script>