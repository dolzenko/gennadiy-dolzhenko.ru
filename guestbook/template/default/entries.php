<?php include_from_template('header.php'); ?>

<?php show_guestbook_add_form(); ?>

<h2>��������� ������</h2>

<p class="entryCount">
�������� ������ � <?php show_entries_start_offset(); ?> �� <?php show_entries_end_offset(); ?> 
(����� �������: <?php show_entry_count(); ?>)
</p>

<?php show_entries(); ?>

<?php include_from_template('footer.php'); ?>