<?php

    include_once 'base_assumptions.inc.php';

    $json_nikki = array(
        "@type" => "Person",
        "givenName" => "Nicola",
        "familyName" => "Easton"
    );

    function markupRecipeJSON($pdo,$id) {
        $query = "SELECT * FROM recipes WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':id',$id,PDO::PARAM_INT);
        $stmt->execute();
        $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
        $markup = array(
            "@context" => "http://schema.org/",
            "@type" => "Recipe",
            "author" => $GLOBALS['json_nikki'],
            "name" => $recipe['title'],
            "image" => $GLOBALS['website_root'] . "/" . $recipe['intro_img'],
            "description" => strip_tags($recipe['intro_html'])
        );
        return json_encode($markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_FORCE_OBJECT);
    }
