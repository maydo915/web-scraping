<form role="search" class="form-horizontal" action="process_search.php" method="post">
    <div class="form-group">
        <label class="control-label col-sm-2" >Keyword</label>
        <div class="col-sm-10">
            <input name="keyword" type="text" class="form-control input-lg" placeholder="Search keyword" />
        </div>                    
    </div>

    <div class="form-group">
        <label class="control-label col-sm-2" >Choose a suburb</label>
        <div class="col-sm-10">
            <?php
            // Retrieve all suburbs
            $sql = "SELECT DISTINCT `suburb`, `state`, `postcode` "
                    . "FROM Properties ORDER BY `suburb`"; 
            $suburbs = getSuburbs($sql);
            ?>
            <select name="suburbOption">       
            <?php
                foreach($suburbs as $index=>$value){
                    echo '<option value="' .$index. '">' .$suburbs[$index]. '</option>' ;
                }
            ?>
        </select>
        </div>                   
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" class="btn btn-default btn-lg" value="Find Properties">
        </div>
    </div>
</form>