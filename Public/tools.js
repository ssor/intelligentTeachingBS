function get_host_url()
{
    var patt1=new RegExp("http://[a-zA-Z0-9\\.\:]+/index.php");
    var crt_href = top.location.href;
    var r = patt1.exec(crt_href);   
    return r[0];
}

