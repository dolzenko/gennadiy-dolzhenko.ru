<?php
/****************************************************************************
 * DRBGuestbook
 * http://www.dbscripts.net/guestbook/
 * 
 * Copyright � 2007-2008 Don B
 ****************************************************************************/

// Field names
$NAME_FIELD_NAME = '���';
$EMAIL_FIELD_NAME = 'E-Mail';
$URL_FIELD_NAME = '��� ����';
$COMMENTS_FIELD_NAME = '��� �����';
$CHALLENGE_FIELD_NAME = "��� �� ��������";

// Form text
$ADD_FORM_LEGEND = '�������� �����';
$ADD_FORM_BUTTON_TEXT = '���������';

// Error text
// The %s directive is a placeholder for the field name and length.
// Use argument swapping if you need to change the order; 
// See http://us.php.net/sprintf for more details.
$ERROR_MSG_BAD_WORD = 'You entered a bad word.';
$ERROR_MSG_MAX_LENGTH = '%s ������ ��������� �� ����� %s ��������.';
$ERROR_MSG_MIN_LENGTH = '%s ������ ��������� ������ %s characters in length.';
$ERROR_MSG_REQUIRED = '���������� ��������� ���� %s.';
$ERROR_MSG_EMAIL = '%s �� �������� ���������� �������� �������.';
$ERROR_MSG_URL_INVALID = '%s �� �������� ���������� URL.';
$ERROR_MSG_URL_BAD_PROTOCOL = '������ HTTP URL �������� �����������.';
$ERROR_MSG_TAGS_NOT_ALLOWED = 'HTML �������� ������ ������ ������.';
$ERROR_MSG_BAD_CHALLENGE_STRING = "�� ����� ������������ ���.";
$ERROR_MSG_URLS_NOT_ALLOWED = "URL ������ ��������� ������ ������ ������.";
$ERROR_MSG_FLOOD_DETECTED = "�� ��������� ���������� ������ ������� �����. ����������, ���������� �����";

?>
