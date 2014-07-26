<?php
require 'res/McStatus.class.php'
?>
<!DOCTYPE html>
<html>
    <head>
        <title>McStatus</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="author" content="Jan Krueger">
        <meta name="publisher" content="Jan Krueger">
        <meta name="copyright" content="Jan Krueger">
        <meta name="description" content="Minecraft and Mojang Servers Status Checker. When you are getting 'Can't connect to minecraft.net' or 'Took too long to log in' errors, this is a great page to check if Minecraft's and Mojang's servers are acting up.">
        <meta name="keywords" content="Minecraft, Mojang, McStatus, Mc, Status, Sweetcode, Yonas, Yonascode, Uptime, Downtime, Online, Offline, Unstable, fast, real, time, minute, check, uptimechecker, onlinecheck, onlinechecker, secure, german, germany, englisch, english, api, publicapi">
        <meta name="page-type" content="Statistik">
        <meta name="audience" content="All">
        <meta http-equiv="content-language" content="en">
        <meta name="robots" content="index, follow">
        <meta name="DC.Creator" content="Jan Krueger">
        <meta name="DC.Publisher" content="Jan Krueger">
        <meta name="DC.Rights" content="Jan Krueger">
        <meta name="DC.Description" content="Minecraft and Mojang Servers Status Checker. When you are getting 'Can't connect to minecraft.net' or 'Took too long to log in' errors, this is a great page to check if Minecraft's and Mojang's servers are acting up.">
        <meta name="DC.Language" content="en">

        <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="css/light/css/custom.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <a class="navbar-brand" href="#">McStatus - This website is not affiliated with Mojang.com or Minecraft.net. </a>
            </div>
        </nav>

        <div id="mojang-news" class="container alert alert-danger" style="visibility:hidden;"></div>

        <div class="container content">
            <p id="last-refresh" style="text-align: center;"><b>Last update: <?php echo "<i>" . McStatus::getTime() . "</i>"; ?></b></p>
            <div class="progress progress-striped active"> 
                <div id="countdown-bar" class="progress-bar" style="width: 0%;"><span id="countdown-to-refresh">Refreshing...</span></div>
            </div>

            <table class="table table-striped table-condensed">
                <thead>
                    <tr>
                        <th id="status" style="text-align: left;">Status</th>
                        <th id="name" style="text-align: left;">Name</th>
                        <th id="uptime" style="text-align: left;">Uptime</th>
                        <th id="type" style="text-align: left;" class="hidden-xs hidden-xss hidden-sm">Type</th>
                        <th id="host" style="text-align: left;" class="hidden-xs hidden-xss hidden-sm">Host</th>
                        <th id="location" style="text-align: left;" class="hidden-xs hidden-xss hidden-sm">Location</th>
                    </tr>
                </thead>
                <tbody>
                     <?php McStatus::renderTable(); ?>
                </tbody>
            </table>
        </div>

        <div class="container footer-content">
            <div class="btn-group left">
                <!-- donate button -->
                <a href="#donateModal" class="btn btn-x btn-primary left" data-toggle="modal"><i class="fa fa-money"></i> Donate</a>

                <!-- cloudflare button -->
                <a href="https://cloudflare.com" target="_blank" class="btn btn-x btn-primary"><i class="fa fa-shield"></i> by <i class="fa fa-cloud"></i></a>

                <!-- history button -->
                <a href="#historyModal" class="hidden-xs hidden-xss hidden-sm btn btn-x btn-primary left" data-toggle="modal"><i class="fa fa-archive"></i> History</a>
            </div>

            <div class="btn-group right">
                <!-- api -->
                <a href="#apiModal" class="btn btn-x btn-primary right" data-toggle="modal"><i class="fa fa-code"></i> API</a>

                <!-- dev -->
                <a href="http://sweetcode.de" class="hidden-xs hidden-xss hidden-sm btn btn-x btn-primary right" target="_blank">SweetCode</a>

                <!-- notification -->
                <a href="#" id="notifi" class="hidden-xs hidden-xss hidden-sm btn btn-x btn-primary right">Enable Notifications</a>

                <!--- website -->   
                <a href="#faqModal" class="btn btn-x btn-primary right" data-toggle="modal"><i class="fa fa-question"></i></a>
            </div>
        </div>

        <!-- API => modal content -->
        <div id="apiModal" class="modal modal-wide fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">PublicAPI (Public Application Programming Interface)</h4>
                    </div>
                    <div class="modal-body">

                        <p><br /><kbd>http://mcstatus.sweetcode.de/publicAPI.php?code=&lt;your-api-code&gt;</kbd></p>
                        <p><br /><kbd>http://mcstatus.sweetcode.de/publicAPI.php?code=&lt;your-api-code&gt;&request=&lt;service&gt;</i></kbd></p>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Minecraft Services</th>
                                    <th>Mojang Services</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <dl class="dl-horizontal">
                                            <dt>minecraft.net</dt>
                                            <dd>Website</dd>
                                            <dt>skins.minecraft.net</dt>
                                            <dd>Skin Service</dd>
                                            <dt>session.minecraft.net</dt>
                                            <dd>Multiplayer Authentication</dd>
                                        </dl>
                                    </td>
                                    <td>
                                        <dl class="dl-horizontal">
                                            <dt>account.mojang.com</dt>
                                            <dd>Account System</dd>
                                            <dt>auth.mojang.com</dt>
                                            <dd>Yggdrassil Login Service</dd>
                                            <dt>authserver.mojang.com</dt>
                                            <dd>Authentication Service</dd>
                                            <dt>sessionserver.mojang.com</dt>
                                            <dd>Session Service</dd>
                                        </dl>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- donate button => modal content -->
        <div id="donateModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Support us to keep this page alive.</h4>
                    </div>
                    <div class="modal-body">
                        <p>This page was made to check <b>Minecraft's and Mojang's servers statuses <i>in real-time</i></b>, and it receives <b>a lot of traffic</b> when either login or session servers are down.
                            There are <b>no advertisements</b>, and <b>there will <i>never</i> be</b>, so we are not getting any profit from this.
                            If you have found this page useful, please donate to keep this page alive.</p>
                        <p id="received-donate-in-modal">Donations Received: 65.32$</p>
                    </div>
                    <div class="modal-footer">
                        <a href="http://goo.gl/lWqG2i" class="btn btn-primary" target="_blank">Donate 5&euro;</a>
                        <a href="http://goo.gl/fPcjoP" class="btn btn-primary" target="_blank">Donate custom amount</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ => modal content -->
        <div id="faqModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">F.A.Q.</h4>
                    </div>
                    <div class="modal-body">
                        <ul class="list-style">
                            <li>
                                <p><b>Who am <i>I</i>?</b></p>
                                <p>I'm a Web and Software Developer from Dortmund, Germany.<br />
                                    I enjoy taking complex problems and turning them into simple and beautiful solutions. I also
                                    love the logic and structure of coding and always strive to write elegant and efficient code,
                                    whether it be HTML, CSS, PHP or Java.<br />
                                    When I'm not coding, you will find me not here...<br />
                                    <b><a href="http://sweetcode.de" target="_blank">SweetCode.de my website</a></b>
                                </p>
                            </li>
                            <li>
                                <p><b>What is <i>McStatus?</i></b></p>
                                <p>Minecraft and Mojang Servers Status Checker. When you are getting 'Can't connect to minecraft.net' or 'Took too long to log in' errors, this is a great page to check if Minecraft's and Mojang's servers are acting up.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- history button => modal content -->
        <div id="historyModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Back to the past.</h4>
                    </div>
                    <div class="modal-body">

                        <select class="form-control">
                            <?php
                                McHistory::getContent(HistoryCSS::OPTIONS);
                            ?>
                        </select>

                        <div class="tab-content">

                            <?php
                            //getHistoryContent($db);
                            ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <script src="js/mcstatus.js"></script>
        <!--<script src="js/notify.js"></script>
        <script type="text/javascript">
            setNews();

            $('[data-toggle="tooltip"]').tooltip({'placement': 'top'});
        </script>-->
    </body>
</html>
