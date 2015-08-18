<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<head>
    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 5px;
        }
        #div-error-warning-message{
            margin-top:10px;
            position:static;
        }
        #btn-install, #img-loader, #div-error-message, #div-warning-message, #div-success-message{
            display:none;
        }
        .btn-change-tab{
            padding-right:10px;
        }
        #div-body{
            margin-left:10px;
            margin-right:10px;
        }
        .tab-content{
            overflow:inherit!important;
        }
        .help-block, .controls ol{
            font-size:small;
        }
        .a-change-tab, .a-change-tab:visited{
            color:#b94a48!important;
            text-decoration: none;
            font-weight:bold;
        }
        .a-change-tab:hover{
            color:#b94a48!important;
            text-decoration: underline;
            font-weight: bold;
        }
    </style>    
</head>
<body>
            <form class="form-horizontal" action="<?php echo site_url('multisite/add_subsite/install'); ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            
                <h3>Site Setting</h3>
                <div class="form-group">
                    <label class="control-label col-md-4" for="subsite">Subsite</label>
                    <div class="controls col-md-8">
                        <input type="text" id="subsite" name="subsite" value="" class="input form-control" placeholder="Subsite">                               
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="aliases">Aliases</label>
                    <div class="controls col-md-8">
                        <input type="text" id="aliases" name="aliases" value="" class="input form-control" placeholder="Aliases, comma separated (e.g: somedomain.com, other.com)">                               
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="logo">Logo</label>
                    <div class="controls col-md-8">
                        <input type="file" id="logo" name="logo" value="" class="input form-control" placeholder="logo">                               
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="description">Description</label>
                    <div class="controls col-md-8">
                        <textarea id="description" name="description" class="input form-control" placeholder="description"></textarea>                               
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="use_subdomain">Use Subdomain</label>
                    <div class="controls col-md-8">
                        <input type="checkbox" id="use_subdomain" name="use_subdomain" class="input" value="true">
                        <p class="help-block">
                            Use subdomain (e.g: subdomain.maindomain.com). This require some DNS setting. Leave it unchecked if you aren't sure.
                        </p>
                        <p>
                            <button id="btn-install" class="btn btn-primary btn-lg" name="Install" disabled="disabled" value="INSTALL NOW">INSTALL NOW</button>
                        </p>
                    </div>
                </div>
                <div id="div-error-warning-message">
                    <div id="div-error-message" class="alert alert-danger">
                        <strong>ERRORS:</strong>
                        <ul id="ul-error-message"></ul>
                    </div>
                    <div id="div-warning-message" class="alert alert-warning">
                        <strong>WARNINGS:</strong>
                        <ul id="ul-warning-message"></ul>
                    </div>
                    <div id="div-success-message" class="alert alert-success">
                        <strong>GREAT !!!</strong>, you can now install <span id="span-subsite"></span> without worrying anything.                        
                    </div>
                                     
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        var REQUEST;
        var RUNNING_REQUEST = false;
        var SUCCESS = false;

        $(document).ready(function(){
            // check things
            check();
        });
        $("input, select").change(function(){
            check();
        });
        $("input:not(#db_name), select").keyup(function(){
            check();
        });

        // next or previous step
        $(".btn-change-tab").click(function(){
            var href = $(this).attr('href');
            $("ul.nav-tabs li").removeClass('active');
            $("div.tab-pane").removeClass('active');
            $("ul.nav-tabs li a[href='"+href+"']").parent().addClass('active');
            $("div.tab-pane[id='"+href.substr(1)+"']").addClass('active');
            return false;
        });

        // from error message
        $(".a-change-tab").live('click', function(){
            var tab = $(this).attr('tab');
            var component = $(this).attr('component');
            $("ul.nav-tabs li").removeClass('active');
            $("div.tab-pane").removeClass('active');
            $("ul.nav-tabs li a[href='"+tab+"']").parent().addClass('active');
            $("div.tab-pane[id='"+tab.substr(1)+"']").addClass('active');
            if(typeof(component) != 'undefined' && component != ''){
                $('#'+component).focus();
            }
            return false;
        });

        $("#btn-install").click(function(){
            $(this).hide();
            $("#img-loader").show();
        });


        function check(){
            if(RUNNING_REQUEST){
                REQUEST.abort();
            }
            RUNNING_REQUEST = true;
            REQUEST = $.ajax({
                type : "POST",
                url : "<?php echo site_url('{{ module_path }}/add_subsite/check'); ?>",
                dataType: "json",
                async : true,
                data : {
                    subsite                 : $("#subsite").val(),
                },
                success : function(response){
                    SUCCESS = response.success;
                    var warning_list = response.warning_list;
                    var error_list = response.error_list;
                    // show error
                    $('#ul-error-message').html('');
                    if(error_list.length>0){
                        for(var i=0; i<error_list.length; i++){
                            var error = error_list[i];
                            $('#ul-error-message').append('<li>'+error+'</li>');
                        }
                        $('#div-error-message').show();
                    }else{
                        $('#div-error-message').hide();
                    }
                    // show warning
                    $('#ul-warning-message').html('');
                    if(warning_list.length>0){
                        for(var i=0; i<warning_list.length; i++){
                            var warning = warning_list[i];
                            $('#ul-warning-message').append('<li>'+warning+'</li>');
                        }
                        $('#div-warning-message').show();
                    }else{
                        $('#div-warning-message').hide();
                    }
                    if(error_list.length==0 && warning_list.length==0){
                        $('#div-success-message').show();
                    }else{
                        $('#div-success-message').hide();
                    }
                    // show/hide button
                    if(SUCCESS){
                        var subsite = $("#subsite").val();
                        var url = '';
                        if($('#use_subdomain').prop('checked')){
                            site_url = '{{ SITE_URL }}';
                            url = site_url.replace('://', '://'+subsite+'.');
                        }else{
                            url = '{{ SITE_URL }}site-'+subsite;
                        }
                        $('#span-subsite').html('<b>'+subsite + '</b> subsite ('+url+')');
                        $('#btn-install').show();
                        $("#btn-install").removeAttr('disabled');
                    }else{
                        $('#btn-install').hide();
                        $("#btn-install").attr('disabled','disabled');
                    }

                },
                error: function(xhr, textStatus, errorThrown){
                    if(textStatus != 'abort'){
                        setTimeout(check, 1000);    
                    }
                }
            });
        }
    </script>
</body>