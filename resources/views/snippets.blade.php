<?php
echo "{% if template == 'product' %} 
<div class='giftwrap' id='".$id."' page='product'>
</div>
{% else if template == 'cart' %}
<div class='giftwrap' id='".$id."' page='cart'>
</div>
{% endif %}
<script src='https://shopifydev.anujdalal.com/zestard_gift_wrap/public/js/giftwrap.js'></script>";
?>
