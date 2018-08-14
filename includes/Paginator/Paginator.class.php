<?php

class Paginator {

    private $pdo;
    private $query;
    private $value_bind_func;
    private $total_items;

    private $page;
    private $limit;
    private $stmt;


    // Provide the PDO object with connection to the database
    // Provide the query with placeholders
    // Provide the callback function which accepts a PDOStatement and binds values to it
    public function __construct($pdo, $query, $value_bind_func, $total_items) {
        $this->pdo = $pdo;
        $this->query  = $query;
        $this->$parameter_bind_func = $value_bind_func;

        // It would be nice if this was automated

        $this->total_items = $total_items;
    }

    // Use prepared statement to get data for this page
    // Return 1 on success, 0 on failure
    public function updatePage($page, $limit) {
        // Store variables
        $this->page = $page;
        $this->limit = $limit;
        // Calculate offset
        $offset = ($page - 1) * $limit
        // Prepared statemnt
        $limit_query = $this->$query . " LIMIT :paginator_limit OFFSET :paginator_offset "

        /*
        $stmt = mysqli_stmt_init($db_conn);
        if (!mysqli_stmt_prepare($stmt,$limit_query)) {
            return 0;
        } else {
            mysqli_stmt_bind_param($stmt,'ii',$limit,$offset);
            mysqli_stmt_execute($stmt);
            $this->data = mysqli_stmt_get_result($stmt);
            return 1;
        }*/

        $this->stmt = $this->pdo->prepare($limit_query);
        call_user_func($value_bind_func,$this->stmt);
        $this->stmt->bindValue(':paginator_limit',$this->limit, PDO::PARAM_INT);
        $this->stmt->bindValue(':paginator_offset',$offset, PDO::PARAM_INT);
        return $this->stmt->execute();
    }

    // Get next row from the data obtained by updatePage()
    public function getNextRow($fetch_style) {
        return $this->stmt->fetch($fetch_style);
    }

    // Returns $num_links pagination links as squential 'li' elements
    // Each element's content is the page number to which it $num_links
    // Each element's class is "$li_class"
    // The current page recieves the class "$li_class $current_page_class"
    public function getPagination($num_links, $li_class, $current_page_class) {

    }

    // Figures out which item is at the top of current page
    // Return page number which displays this item under new limit
    public function getNewLimitPage($newLimit) {
        // Figure out which item is at the top of current page
        // Return page number which displays this item under new limit
        $topItem = $this->limit * ($this->page -1) + 1;
        return (int) ceil($topItem / $newLimit);
    }

}
