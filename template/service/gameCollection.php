<div class="game-collection clearfix row">
    <div class="col-xs-12">
        <h3><?php echo $this->name ?></h3>
    </div>
    <?php foreach ($this->list as $k => $item): ?>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="game-item-list clearfix">
                <a href="<?php echo $this->url('game', array($item->latin)) ?>" class="image pull-left">
                    <img src="<?php echo $this->baseUrl($item->picture) ?>"
                         alt="<?php echo $item->name ?>" class="img-responsive"/>
                </a>
                <div class="box pull-left">
                    <h5><a href="<?php echo $this->url('game', array($item->latin)) ?>">
                            <?php echo $item->name ?></a></h5>
                    <p><?php echo $item->lead ?></p>
                    <?php if ($item->isOnline()): ?>
                        <a href="<?php echo $this->url('game', array($item->latin)) ?>#gameOnline" class="label-online">Играть онлайн</a>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <?php if (($k + 1) % 2 == 0): ?>
            <div class="clearfix"></div>
        <?php endif ?>
    <?php endforeach ?>
</div>