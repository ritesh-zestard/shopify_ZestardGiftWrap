<?php
echo "{% if template == 'product' %} 
<div class='giftwrap' id='".$id."' page='product'>
</div>
{% else if template == 'cart' %}
<div class='giftwrap' id='".$id."' page='cart'>
</div>
{% endif %}
<script src='https://zestardshop.com/shopifyapp/zestard_gift_wrap/js/giftwrap.js'></script>";
?>
