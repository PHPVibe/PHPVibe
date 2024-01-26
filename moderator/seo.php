<?php if (isset($_GET['ac']) && $_GET['ac'] = "remove-logo") {
    update_option('site-logo', '');
    $db->clean_cache();
}
if (isset($_POST['update_options_now'])) {
    foreach ($_POST as $key => $value) {
        if ($key !== "site-logo") {
            update_option($key, $value);
        }
    }
    echo '<div class="msg-info">SEO configuration updated.</div>';

    $db->clean_cache();
}
$all_options = get_all_options();
include_once('setheader.php');

?>

<div class="row">

    <div class="row-setts panel-body">
        <h3>Search engine optimization</h3>
        <form id="validate" class="form-horizontal styled" action="<?php echo admin_url('seo'); ?>"
              enctype="multipart/form-data" method="post">
            <fieldset>
                <input type="hidden" name="update_options_now" class="hide" value="1"/>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Homepage / Global Meta title</label>
                    <div class="controls">
                        <input type="text" name="seo_title" class="col-md-12"
                               value="<?php echo get_option('seo_title'); ?>"/>
                        <span class="help-block" id="limit-text">Title in browser's bar and Google's index.</span>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Homepage / Global meta description</label>
                    <div class="controls">
                        <textarea id="embed" name="seo_desc" class="auto col-md-12"
                                  placeholder="Default: blank"><?php echo get_option('seo_desc'); ?></textarea>
                        <span class="help-block" id="limit-text">The site's default meta description,it appears in search engines.</span>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Channels page</label>
                    <div class="controls">
                        <input type="text" name="BrowseChannels" class="col-md-12"
                               value="<?php echo get_option("BrowseChannels", "Browse Channels"); ?>"/>
                        <span class="help-block" id="limit-text">Title in browser's bar and Google's index for Channels page.</span>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Channels page description</label>
                    <div class="controls">
                        <input type="text" name="BrowseChannelsDesc" class="col-md-12"
                               value="<?php echo get_option("BrowseChannelsDesc", "Channels seo description"); ?>"/>
                        <span class="help-block" id="limit-text">Meta description for Channels page .</span>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Dashboard title</label>
                    <div class="controls">
                        <input type="text" name="DashboardSEO" class="col-md-12"
                               value="<?php echo get_option("DashboardSEO", "Your Dashboard"); ?>"/>
                        <span class="help-block" id="limit-text">User dashboar title .</span>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Single video / media page</label>
                    <div class="controls">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="seo-video-pre" class="col-md-12"
                                       value="<?php echo get_option('seo-video-pre'); ?>"><span
                                        class="help-block"><strong>Prefix / Before</strong> the title</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="seo-video-post" class="col-md-12"
                                       value="<?php echo get_option('seo-video-post'); ?>"><span
                                        class="help-block align-center"><strong>Suffix / After</strong> the title</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Video/Music Category </label>
                    <div class="controls">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="seo-channel-pre" class="col-md-12"
                                       value="<?php echo get_option('seo-channel-pre'); ?>"><span
                                        class="help-block"><strong>Prefix / Before</strong> the title</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="seo-channel-post" class="col-md-12"
                                       value="<?php echo get_option('seo-channel-post'); ?>"><span
                                        class="help-block align-center"><strong>Suffix / After</strong> the title</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Image Channel / Image Category </label>
                    <div class="controls">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="seo-image-channel-pre" class="col-md-12"
                                       value="<?php echo get_option('seo-image-channel-pre'); ?>"><span
                                        class="help-block"><strong>Prefix / Before</strong> the title</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="seo-image-channel-post" class="col-md-12"
                                       value="<?php echo get_option('seo-image-channel-post'); ?>"><span
                                        class="help-block align-center"><strong>Suffix / After</strong> the title</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Playlist</label>
                    <div class="controls">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="seo-playlist-pre" class="col-md-12"
                                       value="<?php echo get_option('seo-playlist-pre'); ?>"><span
                                        class="help-block"><strong>Prefix / Before</strong> the title</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="seo-playlist-post" class="col-md-12"
                                       value="<?php echo get_option('seo-playlist-post'); ?>"><span
                                        class="help-block align-center"><strong>Suffix / After</strong> the title</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Static Page</label>
                    <div class="controls">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="seo-page-pre" class="col-md-12"
                                       value="<?php echo get_option('seo-page-pre'); ?>"><span
                                        class="help-block"><strong>Prefix / Before</strong> the title</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="seo-page-post" class="col-md-12"
                                       value="<?php echo get_option('seo-page-post'); ?>"><span
                                        class="help-block align-center"><strong>Suffix / After</strong> the title</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Blog Post</label>
                    <div class="controls">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="seo-post-pre" class="col-md-12"
                                       value="<?php echo get_option('seo-post-pre'); ?>"><span
                                        class="help-block"><strong>Prefix / Before</strong> the title</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="seo-post-post" class="col-md-12"
                                       value="<?php echo get_option('seo-post-post'); ?>"><span
                                        class="help-block align-center"><strong>Suffix / After</strong> the title</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group form-material">
                    <label class="control-label"><i class="icon-search"></i>Single user profile</label>
                    <div class="controls">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="seo-profile-pre" class="col-md-12"
                                       value="<?php echo get_option('seo-profile-pre'); ?>"><span
                                        class="help-block"><strong>Prefix / Before</strong> the title</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="seo-profile-post" class="col-md-12"
                                       value="<?php echo get_option('seo-profile-post'); ?>"><span
                                        class="help-block align-center"><strong>Suffix / After</strong> the title</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row page-footer">
                    <button class="btn btn-large btn-primary pull-right"
                            type="submit"><?php echo _lang("Update settings"); ?></button>
                </div>
            </fieldset>
        </form>
    </div>
</div>
