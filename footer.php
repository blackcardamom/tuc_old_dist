        <div class="footer">
            <a href="index.html"><img src="logos/the_ugly_croissant_long_EDIT.jpeg"></a>
            <a href="mailto:theuglycroissant@gmail.com"><i class="fas fa-envelope"></i></a>
            <a href="https://www.instagram.com/theuglycroissant/"><i class="fab fa-instagram"></i></a>
        </div>
    </body>
    <script>
        function myFunction() {
            var x = document.getElementById("myTopnav");
            if (x.className === "topnav") {
                x.className += " responsive";
            } else {
                x.className = "topnav";
            }
        }

        function copyToClip() {
            var dummy = document.createElement('input');
            text = window.location.href;
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
        }
    </script>
</html>
