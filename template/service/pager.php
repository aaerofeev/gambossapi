<?php if (count($this->list) > 1): ?>
<ul class="pagination">
    <?php if ($this->prev): ?>
    <li>
        <a href="<?php echo $this->prev ?>" aria-label="First">
            <span aria-hidden="true">&laquo;</span>
        </a>
    </li>
    <?php else: ?>
    <li class="disabled">
        <a href="#" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
        </a>
    </li>
    <?php endif ?>

    <?php foreach ($this->list as $page => $url): ?>
    <li<?php if ($page == $this->currentPage): ?> class="active"<?php endif ?>>
        <a href="<?php echo $url ?>"><?php echo $page ?></a>
    </li>
    <?php endforeach ?>

    <?php if ($this->next): ?>
    <li>
        <a href="<?php echo $this->next ?>" aria-label="Last">
            <span aria-hidden="true">&raquo;</span>
        </a>
    </li>
    <?php else: ?>
    <li class="disabled">
        <a href="#" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
        </a>
    </li>
    <?php endif ?>
</ul>
<?php endif ?>