<div class="game-collection-rating clearfix row">
    <div class="col-xs-12">
        <h3><?php echo $this->name ?></h3>
    </div>
    <?php foreach ($this->list as $k => $item): ?>

    <div class="col-xs-12">
        <div class="game-item-rating clearfix">
            <h5>
                <span class="place"><?php echo $k + 1 ?>.</span>
                <a href="<?php echo $this->url('game', array($item->latin)) ?>">
                    <?php echo $item->name ?>
                </a>
            </h5>
            <p><?php echo $item->lead ?></p>
        </div>
    </div>

    <?php endforeach ?>
</div>