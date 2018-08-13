<?php

class Paginator {

    private $db_conn;
    private $query;
    private $total_items

    private $page;
    private $limit;
    private $data;

    public function __construct($db_conn, $query) {
        $this->db_conn = $db_conn;
        $this->query  = $query;

        $this->total_items = mysqli_num_rows(mysqli_query($db_conn,$query));
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
        $limit_query = $this->$query . " LIMIT ? OFFSET ? "
        $stmt = mysqli_stmt_init($db_conn);
        if (!mysqli_stmt_prepare($stmt,$limit_query)) {
            return 0;
        } else {
            mysqli_stmt_bind_param($stmt,'ii',$limit,$offset);
            mysqli_stmt_execute($stmt);
            $this->data = mysqli_stmt_get_result($stmt);
            return 1;
        }
    }

    // Get next row from the data obtained by updatePage()
    public function getNextRow() {
        return mysqli_fetch_assoc($data);
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
