</div>
<div style="clear:both"></div>
<button onclick="topFunction()" id="myBtn" title="Go to top">Наверх</button>
<div id="footer">
    <div style="float:left;margin-top: 11px;padding-left: 11px; font-size:12px; font-weight:bold;"><?php echo $month_to_look; ?></div>
    <div id="copy_right">Учет электроэнергии пром. отдел 2009</div>
</div>
<div id="bottom_shadow"></div>
</div>
</div>
<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
        } else {
            document.getElementById("myBtn").style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>
</body>
</html>
