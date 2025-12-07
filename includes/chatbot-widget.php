<?php
/**
 * Chatbot Widget Include
 * Include file này vào các trang để hiển thị chatbot
 */
?>

<!-- Chatbot CSS -->
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/chatbot.css">

<!-- Chatbot JavaScript -->
<script src="<?php echo BASE_URL; ?>/assets/js/chatbot.js" defer></script>

<!-- Font Awesome (nếu chưa có) -->
<?php if (!defined('FONT_AWESOME_LOADED')): ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php define('FONT_AWESOME_LOADED', true); endif; ?>
