<?php include_from_template('header.php'); ?>

<?php show_guestbook_add_form(); ?>

<h2>Последние записи</h2>

<p class="entryCount">
Показаны записи с <?php show_entries_start_offset(); ?> по <?php show_entries_end_offset(); ?> 
(Всего записей: <?php show_entry_count(); ?>)
</p>

<?php show_entries(); ?>

<?php include_from_template('footer.php'); ?>