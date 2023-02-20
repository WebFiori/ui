<div>
    <?php 
    if (count($posts) != 0) {?>
    <ul>
    <?php
        foreach ($posts as $postTitle) {?>
        <li><?php echo $postTitle;?></li>
        <?php
        }
        ?>
    </ul>
    <?php
    } else {
        echo "No posts.\n";
    }
    ?>
</div>

