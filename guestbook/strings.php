<?php
/****************************************************************************
 * DRBGuestbook
 * http://www.dbscripts.net/guestbook/
 * 
 * Copyright © 2007-2008 Don B
 ****************************************************************************/

// Field names
$NAME_FIELD_NAME = 'Имя';
$EMAIL_FIELD_NAME = 'E-Mail';
$URL_FIELD_NAME = 'Веб сайт';
$COMMENTS_FIELD_NAME = 'Ваш отзыв';
$CHALLENGE_FIELD_NAME = "Код на картинке";

// Form text
$ADD_FORM_LEGEND = 'Написать отзыв';
$ADD_FORM_BUTTON_TEXT = 'Отправить';

// Error text
// The %s directive is a placeholder for the field name and length.
// Use argument swapping if you need to change the order; 
// See http://us.php.net/sprintf for more details.
$ERROR_MSG_BAD_WORD = 'You entered a bad word.';
$ERROR_MSG_MAX_LENGTH = '%s должен содержать не более %s символов.';
$ERROR_MSG_MIN_LENGTH = '%s должен содержать хотябы %s characters in length.';
$ERROR_MSG_REQUIRED = 'Необходимо заполнить поле %s.';
$ERROR_MSG_EMAIL = '%s не является допустимым почтовым адресом.';
$ERROR_MSG_URL_INVALID = '%s не является допустимым URL.';
$ERROR_MSG_URL_BAD_PROTOCOL = 'Только HTTP URL являются допустимыми.';
$ERROR_MSG_TAGS_NOT_ALLOWED = 'HTML запрещен внутри текста отзыва.';
$ERROR_MSG_BAD_CHALLENGE_STRING = "Вы ввели неправильный код.";
$ERROR_MSG_URLS_NOT_ALLOWED = "URL адреса запрещены внутри текста отзыва.";
$ERROR_MSG_FLOOD_DETECTED = "Вы пытаетесь отправлять отзывы слишком часто. Пожалуйста, попробуйте позже";

?>
