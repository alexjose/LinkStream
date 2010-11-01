<?php
session_start();
include_once 'inc/Link.php';
include_once 'inc/User.php';
include_once 'inc/Stream.php';
$link_obj = new Link();
$user_obj = new User();
$stream_obj = new Stream();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <link rel="shortcut icon" href="http://portfolio.alexjose.in/linkstream/favicon.ico" type="image/x-icon" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Link Stream</title>

        <link rel="stylesheet" href="css/style.css" type="text/css" />
        <link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.5.custom.css" type="text/css" />
        <script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.5.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.watermarkinput.js"></script>


    </head>
    <script type="text/javascript">
        // <![CDATA[
        $(document).ready(function(){
            setTimeout('showNewLinks()', 1000);
            $( "#accordion" ).accordion();
            $('.cat_button').button();

            $( "#dialog-url" ).dialog({
                height: 140,
                autoOpen: false,
                modal: true,
                resizable: false
            });
            $( "#dialog-category" ).dialog({
                height: 140,
                autoOpen: false,
                modal: true,
                resizable: false
            });
            $( "#dialog-bookmark" ).dialog({
                height: 140,
                width: 480,
                autoOpen: false,
                modal: true,
                resizable: false
            });
            $( "#sel_link" ).dialog({
                height: 200,
                width: 740,
                autoOpen: false,
                modal: true,
                resizable: false
            });
            $('.closes').click(function(){
                $('#loader').html('<div align="center" id="load" style="display:none"><img src="css/images/load.gif" /></div>');
                $('#url').val("http://");
            });
<?php if (!isset($_SESSION['signed'])) { ?>
            $('#url').focus(function(){
                $('#signin_inform').slideDown();

            });
            $('#url').blur(function() {
                $('#signin_inform').fadeOut(2000);
            });
<?php } ?>

        $('#share').live("click",function(){
            var new_link = $('#newlink').serialize();
            //alert(new_link);
            var img_id = $('#cur_image').val();
            //alert(img_id);
            var cat = $('#category').val();
            if(cat=='Select Category'){
                $( "#dialog-category" ).dialog( "open" );
                $('#category').focus();
                return false;
            }
            var t = $('#topic').val();
            var img_src = $('img#'+img_id).attr('src');
            //alert(img_src);
            var post_datas = new_link + '&cat=' + cat + '&t=' + t + '&img='  + img_src;
            //alert(post_datas);
            $('#loader').prepend('<div align="center" id="load" style="display:none"><img src="css/images/load.gif" /></div>');
            $('#load').show();
            $.post("link_proc.php", post_datas,function(data){
                //alert('Sent');
                //alert(data);
//                $('#loader').append(data);
                $('#loader').html('<div align="center" id="load" style="display:none"><img src="css/images/load.gif" /></div>');
                $('#url').val("http://");
            });
        });

        // delete event
        $('#attach').live("click", function(){
		
            if(!isValidURL($('#url').val()))
            {
                $( "#dialog-url" ).dialog( "open" );
                return false;
            }
            else
            {
                $('#load').show();
                $.post("fetch.php?url="+$('#url').val(), {
                }, function(response){
                    $('#loader').html($(response).fadeIn('slow'));
                    $('.images img').hide();
                    $('#load').hide();
                    $('img#1').fadeIn();
                    $('#cur_image').val(1);

                    $( "#topic").autocomplete({
                        source: "ajax/topic_search.php"
                    });
                });
            }
        });
        // next image
        $('#next').live("click", function(){
		
            var firstimage = $('#cur_image').val();
            if(firstimage < $('#total_images').val())
            {
                $('img#'+firstimage).hide();
                firstimage = parseInt(firstimage)+parseInt(1);
                $('#cur_image').val(firstimage);
                $('img#'+firstimage).fadeIn();
            }
        });
        // prev image
        $('#prev').live("click", function(){
            var firstimage = $('#cur_image').val();
                
            if(firstimage>1)
            {
                $('img#'+firstimage).hide();
                firstimage = parseInt(firstimage)-parseInt(1);
                $('#cur_image').val(firstimage);
                $('img#'+firstimage).fadeIn();
            }
        });
        
        $('#bookmark').click(function(){
            $( "#dialog-bookmark" ).dialog( "open" );
            return false;
        })

        // watermark input fields
        jQuery(function($){
            $("#url").Watermark("http://");
        });
        jQuery(function($){
            $("#url").Watermark("watermark","#369");
			
        });
        function UseData(){
            $.Watermark.HideAll();
            $.Watermark.ShowAll();
        }

<?php if (isset($_REQUEST['url'])) { ?>
            $('#attach').click();
<?php } ?>

    });
	
    function isValidURL(url){
        var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	
        if(RegExp.test(url)){
            return true;
        }else{
            return false;
        }
    }
    function showNewLinks(){
        //            alert('hi');
        var id = $(".link").first().attr('id').replace('link-', '');
        //            alert(id);
        var suffix ='';
<?php if (isset($_GET['c'])) { ?>
            suffix = '&c=<?php echo $_GET['c'] ?>';
<?php } elseif (isset($_GET['t'])) { ?>
            suffix = '&t=<?php echo $_GET['t'] ?>';
<?php } elseif (isset($_GET['u'])) { ?>
            suffix = '&u=<?php echo $_GET['u'] ?>';
<?php } ?>

        $.post('link_feed.php?id='+id+suffix, function(data){
            if(data!=''){
                $('#links').prepend(data);
                id = $(".link").first().attr('id').replace('link-', '');
                var height = $('#link-'+id).height();
                $('#link-'+id).hide()
                $('#link-'+id).slideDown(1500);
                //                    $('#link-'+id).hide().css({height : 0});
                //                    $('#link-'+id).show().animate({height : height}, {duration : 1500});

                setTimeout('showNewLinks()',1000);
            }else{
                var count = ($('#links > div').length);
                    
                setTimeout('showNewLinks()',4000);
            }
                    
        });
    };

    $('.link').live('click', function(){
        //            $('#sel_link').hide();
        $('#sel_link').html($(this).html());
        $( "#sel_link" ).dialog( "open" );
        //            $('#sel_link').fadeIn('slow');
        //            $('#sel_link > #count').append('Hi');
        //            $(document).scrollTop(0);
    });
    
    $('.stream_button').live('click', function(){
        var linkid = $(this).attr('id');
        post_datas = "link_id=" + linkid;
        $.post("link_proc.php", post_datas, function(data){
            //            $('#loader').append(data);
            $('#loader').html('<div align="center" id="load" style="display:none"><img src="css/images/load.gif" /></div>');
            $('#url').val("http://");
        });
        return false;
    });

    

    // ]]>
    </script>
    <body>
        <div id="header">
            <div id="streams">
                <a href="/linkstream" id="home" class="cat_button">Home</a>
                <?php
                $cats = $stream_obj->get_Categories();
                foreach ($cats as $cat) {
                ?>
                    <a href="?c=<?php echo strtolower($cat[0]) ?>" id="<?php echo strtolower($cat[0]) ?>" class="cat_button"><?php echo ucwords($cat[0]) ?></a>
                <?php } ?>
            </div>
            <?php
                if (!isset($_SESSION['signed']))
                    include 'start.php';
                else {
            ?>

                    Howdy, <a href="?u=<?php echo $user_obj->get_Name(@$_SESSION['uid']) ?>"><?php echo $user_obj->get_Name(@$_SESSION['uid']) ?></a> <a href="signout.php">Sign Out</a>
            <?php } ?>
            </div>
            <div align="center">
                <br />
                <div id="heading">Link Stream Beta</div>
                <input type="hidden" name="cur_image" id="cur_image" />

                <div class="box" align="left">
                    <div class="head">Link</div>
                    <br clear="all" /><br clear="all" />
                    <input type="text" name="url" size="64" id="url" value="<?php echo @$_REQUEST['url'] ?>" /><div class="closes"></div><input type="button" name="attach" value="Attach" id="attach" />
                <?php if (!isset($_SESSION['signed'])) {
                ?>
                    <div id="signin_inform">You are not signed in. <?php include 'start.php' ?></div>
                <?php } ?>
                <br clear="all" />
                <div id="loader">
                    <div align="center" id="load" style="display:none"><img src="css/images/load.gif" /></div>
                </div>
                <br clear="all" />
            </div>

            <br />
            <div class="wrap" align="center">
                <div id="content">
                    <div id="content_head">
                        Live Stream
                        <?php
                        if (isset($_GET['c']))
                            echo 'for ' . ucwords($_GET['c']);
                        elseif (isset($_GET['t']))
                            echo 'for ' . ucwords($_GET['t']);
                        elseif (isset($_GET['u']))
                            echo 'for ' . ucwords($_GET['u']);
                        ?>
                    </div>
                    <div id="sidebar">
                        <a href="javascript:function inbtwn(a,b,c){ try{ var a1 = a.split(b); var a2 = a1[1].split(c); return a2[0]; }catch(err){return '';} } function inbtwn2(a,b,c){ try{ var a1 = a.split(b); var a2 = a1[2].split(c); return a2[0]; }catch(err){return '';} } var src = document.body.innerHTML.toString(); if(window.location.toString().toLowerCase().match('youtube.com/')){ var v = inbtwn(src, 'video_id=', '&');var t = inbtwn(src, '&t=', '&');var fl = inbtwn(src, '&fmt_list=', '&');var title = inbtwn2(src, 'title=%22', '%22');if(title == ''){ title = inbtwn(src, '%3Ch1 id=%22vt%22%3E', '%3C'); };if(v != '' && t != ''){document.location='http://portfolio.alexjose.in/linkstream?url=http://www.youtube.com/watch%3Fv%3D'+v+'&vid='+v+'&tid='+t+'&flid='+fl+'&titleid='+title;}else{document.location='http://portfolio.alexjose.in/linkstream?url='+escape(window.location);}}else{ document.location='http://portfolio.alexjose.in/linkstream?url='+escape(window.location); }" id="bookmark" class="cat_button" alt="Stream It" title="Stream It" style="width: 100%;">Stream It</a>
                    </div>
                    <div id="links">
                        <?php include 'link_feed.php' ?>
                    </div>

                    <div id="sidebar">
                        <h2>Trending</h2>

                        <div id="accordion">
                            <?php
                            $cats = $stream_obj->get_Categories();
                            foreach ($cats as $cat) {
                            ?>
                                <h3><a href="#"><?php echo $cat[0] ?></a></h3>
                                <div>
                                    <ul>
                                    <?php
                                    $trends = $stream_obj->get_TrendsInCat($cat[0]);
                                    foreach ($trends as $trend) {
                                    ?>
                                        <li><a href="?t=<?php echo $trend[0] ?>"><?php echo $trend[0] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>

                    </div>
                    <br clear="all" />
                </div>
            </div>
            <br clear="all" />
        </div>
        <div id="dialog-url" title="Invalid URL">
            <p>Please enter a valid url.</p>
        </div>
        <div id="dialog-category" title="Choose Category">
            <p>Please select a Category.</p>
        </div>
        <div id="dialog-bookmark" title="Stream It Bookmark">
            <p>Drag the <strong>Stream It</strong> button to Bookmarks Toolbar. Click <strong>Stream It</strong> when you want to share the site your are browsing.</p>
        </div>
        <div id="sel_link">
        </div>
        <div id="footer">
            About ||<a href="widgets/index.html">Widgets</a>
        </div>
    </body>
</html>