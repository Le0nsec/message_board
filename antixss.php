<?php
header("content-type:text/html;charset=utf-8");
function antixss($content)
{
    //$white_list = "/^[A-Za-z0-9_\-!?%=()+（）！？，,。.《》；;‘’']+$/im";
    $black_list = "/script|javascript|href|lowsrc|bgsound|onerror|onclick|style|\*|&|#|\\\|sleep/im";
    if(preg_match($black_list, $content)) die("<script>alert('非法字符');window.location.href='index.php';</script>");
    $str = htmlspecialchars($content);
    return $str;
}

?>