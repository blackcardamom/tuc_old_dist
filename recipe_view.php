<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
    <link rel="stylesheet" href="tuc_poc.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>The Ugly Croissant - Lemon Meringue Pie</title>
</head>

<body id="myBody">
    <div class="topnav" id="myTopnav">
        <a href="index.html"><img src="logos/the_ugly_croissant_long_EDIT.jpeg"></a>
        <a href="about.html">About</a>
        <a href="recipes.html" class="active_topnav">Recipes</a>
        <a href="gallery.html">Gallery</a>
        <a href="contact.html">Contact</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()"> <i class="fas fa-bars"></i></a>
    </div>
    <div class="recipe_intro">
        <div class="recipe_intro_img">
            <img src="recipes/lemon_meringue/picture.jpg">
        </div>
        <h1>Lemon Meringue Pie</h1>
        <p><i class="fas fa-clock"></i> 2 hours + 2 hours cooling time &nbsp;&nbsp; <i class="fas fa-utensils"></i> 8 servings &nbsp;&nbsp;<br id="mobile_linebreak"><br id="mobile_linebreak">
            <i class="fab fa-facebook button" title="Share to Facebook"></i> &nbsp;&nbsp; <i class="fab fa-twitter button" title="Share to Twitter"></i> &nbsp;&nbsp; <i class="fab fa-pinterest button" title="Share to Pinterest"></i> &nbsp;&nbsp;
            <i class="fab fa-reddit button" title="Share to Reddit"></i> &nbsp;&nbsp; <i class="fas fa-copy button" title="Copy to clipboard" onclick="copyToClip()"></i> &nbsp;&nbsp; <i class="fas fa-print button" title="Print recipe"></i></p>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent sed nulla et libero sodales vestibulum. Etiam ac purus ante. Vestibulum non urna dictum, pharetra nunc vitae, cursus massa. Suspendisse tempus et mi quis dignissim. Aliquam at massa
            ipsum. Nulla viverra tortor sit amet ullamcorper pharetra. Donec sit amet erat blandit, malesuada lorem vel, tristique purus. Proin semper nec elit ac bibendum.</p>
    </div>
    <div class="recipe_content_container">
        <div class="ingredients">
            <h2>Ingredients</h2>
            <h3>For the pastry</h3>
            <ul>
                <li>100g plain flour</li>
                <li>70g unsalted butter</li>
                <li>80g caster sugar</li>
                <li>1 egg, beaten with a fork</li>
            </ul>
            <h3>For the lemon curd</h3>
            <ul>
                <li>6 lemons (juice of 6, zest of 2)</li>
                <li>180g sugar</li>
                <li>110g butter</li>
                <li>2tbsp cornflour</li>
                <li>2tbsp water</li>
                <li>200g condensed milk</li>
            </ul>
            <h3>For the merignue</h3>
            <ul>
                <li>2 egg whites</li>
                <li>110g caster sugar</li>
            </ul>
        </div>
        <div class="method">
            <h2>Method</h2>
            <h3>Make the curd</h3>
            <ol>
                <li>Dissolve the cornflour in the water in a small bowl and put to the side.</li>
                <li>In a medium pan, rub zest together with 2tsp of the sugar until a paste forms. Add the rest of the sugar, butter and juice. Bring to the boil and boil whilst stirring for about 4-5 minutes, stirring throughout.</li>
                <li>Turn down the heat to a simmer. Add the cornflour and simmer for 3-4 minutes or until thick.</li>
                <li>Take off of the heat and put in a bowl. Add the condensed milk.</li>
                <li>Cool for at least 2hrs.</li>
            </ol>
            <h3>Make the pastry</h3>
            <ol>
                <li>Put the flour, sugar and cubed butter into a bowl and rub together with fingertips until breadcrumbs form.</li>
                <li>Add the egg and bind with a knife until it comes together. If it’s still very crumbly then add a tsp of water at a time until it comes together. Rest for at least 30 mins (preferably an hour).</li>
                <li>Preheat oven to 180ºC. </li>
                <li>Roll out the pastry to the size of your pastry case. Line the case and refrigerate for 30 mins.</li>
                <li>Blind bake the pastry: Crumple up some baking paper that is a bit bigger than your pastry case and place on the pastry case. Fill with baking beans or rice. Cook for 10 mins. Remove the paper and beans and then cook for another 8-10 mins
                    or until the bottom of the tart case is dry/ the edges are starting to brown.</li>
            </ol>
            <h3>Make the meringue</h3>
            <ol>
                <li>Once the pastry is cooked and the lemon curd is set, make the meringue. Place the egg whites in a clean bowl and whisk until stiff peaks form.</li>
                <li>Then gradually whisk in the sugar until the meringue is glossy and had stiff peaks.</li>
            </ol>
            <h3>Assembly</h3>
            <p>Place the lemon curd in the baked pastry case. Top with the meringue, making sure that all edges are covered so that you cannot see any curd. Sprinkle the meringue with caster sugar and place in the oven for about 5 mins until the top is crispy.</p>
        </div>
    </div>
    <div class="footer">
        <a href="index.html"><img src="logos/the_ugly_croissant_long_EDIT.jpeg"></a>
        <a href="mailto:theuglycroissant@gmail.com"><i class="fas fa-envelope"></i></a>
        <a href="https://www.instagram.com/theuglycroissant/"><i class="fab fa-instagram"></i></a>
    </div>
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
</body>

</html>
