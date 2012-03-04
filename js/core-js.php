<script type="text/javascript">
            $('document').ready(function(){
                var index = 0;
                var images = $('#recent li');
                $($(images).get(index)).fadeIn('slow');
                window.setInterval(function(){
                    $($(images).get(index)).fadeOut('slow', function(){
                        if(index == images.length - 1){
                            index = 0;
                        }else{
                            index++;
                        }

                        $($(images).get(index)).fadeIn('slow');
                    });
				}, 6000);
            });

            function confirmSubmit()  {
                var agree=confirm("Are you sure you wish to delete this?");
                if (agree)
                    return true ;
                else
                    return false ;
            }
            function deniedcommentClose() {
                document.getElementById("denied_popup").style.display = "none";
            }
            function requestcommentClose() {
                document.getElementById("request_popup").style.display = "none";
            }
            function TAlimit(s) {
                var maxlength = 255; // Change number to your max length.
                if (s.value.length > maxlength)
                    s.value = s.value.substring(0,maxlength);
            }
            function ShowSeriesName(id) {
                // First, hide all of the series names
<?php
## Make a hide statement for each language
        foreach ($languages AS $langid => $langname) {
            print "document.seriesform.SeriesName_" . $langid . ".style.display='none';\n";
        }
?>
        // Then, display the one we want
        var objectname = eval("document.seriesform.SeriesName_" + id);
        objectname.style.display='inline';
    }
    function ShowSeriesOverview(id) {
        // First, hide all of the series overviews
<?php
## Make a hide statement for each language
        foreach ($languages AS $langid => $langname) {
            print "document.seriesform.Overview_" . $langid . ".style.display='none';\n";
        }
?>
        // Then, display the one we want
        var objectname = eval("document.seriesform.Overview_" + id);
        objectname.style.display='inline';
    }
    function ShowEpisodeName(id) {
        // First, hide all of the series names
<?php
## Make a hide statement for each language
        foreach ($languages AS $langid => $langname) {
            print "document.episodeform.EpisodeName_" . $langid . ".style.display='none';\n";
        }
?>
        // Then, display the one we want
        var objectname = eval("document.episodeform.EpisodeName_" + id);
        objectname.style.display='inline';
    }
    function ShowEpisodeOverview(id) {
        // First, hide all of the series overviews
<?php
## Make a hide statement for each language
        foreach ($languages AS $langid => $langname) {
            print "document.episodeform.Overview_" . $langid . ".style.display='none';\n";
        }
?>
        // Then, display the one we want
        var objectname = eval("document.episodeform.Overview_" + id);
        objectname.style.display='inline';
    }
    var globalShowSeriesName = this.ShowSeriesName;
    var globalShowSeriesOverview = this.ShowSeriesOverview;
    var globalShowEpisodeName = this.ShowEpisodeName;
    var globalShowEpisodeOverview = this.ShowEpisodeOverview;

    // Function to open a popup and allow child to send data back
    function openChild(file,window, dimX, dimY) {
        childWindow=open(file,window,'resizable=1,location=0,status=0,scrollbars=1,width=' + dimX + ',height=' + dimY);
        if (childWindow.opener == null) childWindow.opener = self;
    }

    var checkobj

    // User ratings (turns stars on and off)
    function UserRating(rating)  {
        for (i=1; i<=10; i++)  {
            if (i <= rating)  {
                var thisimage = eval("document.images.userrating" + i);
                thisimage.src = '<?= $baseurl ?>/images/star_on.png';
            }
            else  {
                var thisimage = eval("document.images.userrating" + i);
                thisimage.src = '<?= $baseurl ?>/images/star_off.png';
            }
        }
    }
    // User ratings (turns stars on and off)
    function UserRating2(prefix,rating)  {
        for (i=1; i<=10; i++)  {
            if (i <= rating)  {
                var thisimage = eval("document.images." + prefix + i);
                thisimage.src = '<?= $baseurl ?>/images/game/star_on.png';
            }
            else  {
                var thisimage = eval("document.images." + prefix + i);
                thisimage.src = '<?= $baseurl ?>/images/game/star_off.png';
            }
        }
    }

    //Function to toggle an element
    function toggleDiv(divid){
        // if(document.getElementById(divid).style.display == 'none'){
            // document.getElementById(divid).style.display = 'block';
        // }else{
            // document.getElementById(divid).style.display = 'none';
        // }
		$('#' + divid).slideToggle(500);
    }

    // Site Terms Agreement Function
    function agreesubmit(el){
        checkobj=el
        if (document.all||document.getElementById){
            for (i=0;i<checkobj.form.length;i++){  //hunt down submit button
                var tempobj=checkobj.form.elements[i]
                if(tempobj.type.toLowerCase()=="submit")
                    tempobj.disabled=!checkobj.checked
            }
        }
    }
    // Site Terms Agreement Function
    function defaultagree(el){
        if (!document.all&&!document.getElementById){
            if (window.checkobj&&checkobj.checked)
                return true
            else{
                alert("Please read/accept terms to submit form")
                return false
            }
        }
    }
    // -->
        </script>

        <script type="text/javascript" src="<?php echo $baseurl; ?>/niftycube.js"></script>
        <script type="text/javascript">
            window.onload=function(){
                Nifty("DIV.section","big");
                Nifty("DIV.footer","big");
                Nifty("DIV.titlesection","big");
            }
        </script>
        <script type="text/javascript">
            function hideElement (elementId) {
                var element;
                if (document.all)
                    element = document.all[elementId];
                else if (document.getElementById)
                    element = document.getElementById(elementId);
                if (element && element.style)
                    element.style.display = 'none';
            }
            function showElement (elementId) {
                var element;
                if (document.all)
                    element = document.all[elementId];
                else if (document.getElementById)
                    element = document.getElementById(elementId);
                if (element && element.style)
                    element.style.display = '';
            }
            function DisplayImporterRow (importerValue)  {
                if (importerValue == 'tv.com')
                    showElement('tvcom');
                else
                    hideElement('tvcom');
            }
        </script>