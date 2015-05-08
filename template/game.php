<div class="tile-view-caption">
    Просмотр игры
</div>

<div class="game-view clearfix tile-view row">

    <div class="row game-info">
        <div class="col-md-4 col-sm-12 col-xs-12">
            <div class="row">

                <div class="col-xs-6 col-sm-6 col-md-12">

                <img src="<?php echo $this->baseUrl($this->game->picture) ?>"
                     alt="<?php echo $this->game->lead ?>" class="img-responsive img-main"/>

                </div>

                <div class="col-xs-6 col-sm-6 col-md-12">

                <?php if ($this->game->embed): ?>
                    <a href="#gameOnline" class="btn btn-primary btn-block">Играть сейчас</a>
                <?php endif ?>

                <?php foreach ($this->game->links as $link): ?>
                <a href="<?php echo $this->baseUrl($link['url']) ?>" class="btn btn-success btn-block">
                    Скачать
                    <strong><?php echo $link['name'] ?></strong>
                </a>
                <?php endforeach ?>

                </div>

            </div>

            <p class="game-help">
                <?php echo $this->gameText ?>
            </p>

            <!-- TODO позиция для баннера -->
        </div>

        <div class="col-md-8 col-sm-12 col-xs-12">
            <h3><?php echo $this->game->name ?></h3>
            <p><?php echo nl2br($this->game->desc) ?></p>
        </div>
    </div>

    <?php if ($this->game->screen): ?>
        <h4>Игровой процесс</h4>
        <div class="row image-list">
            <?php foreach ($this->game->screen as $shot): ?>
            <div class="col-md-4 img-view">
                <a href="<?php echo $this->baseUrl($shot['picture']) ?>"
                   data-toggle="lightbox"
                   data-gallery="gameGallery"
                   data-parent=".image-list"
                   data-title="<?php echo $this->game->name ?>"
                   data-footer="<?php echo $this->game->lead ?>">
                    <img src="<?php echo $this->baseUrl($shot['picture']) ?>"
                         alt="<?php echo $this->game->lead ?>" class="img-responsive"/>
                </a>
            </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <?php if ($this->game->embed): ?>
    <div class="game-embed">
        <h4 id="gameOnline">Играть онлайн</h4>
        <div class="game-embed-container">
            <?php echo $this->game->embed ?>
        </div>
        <div class="game-embed-text">
            <p>
                Играть онлайн в браузере игру «<?php echo $this->game->name ?>» вы сможете сразу после конца подгрузки.
                <?php if ($this->game->links):?>
                На нашем сайте вы можете скачать полную версию игры «<?php echo $this->game->name ?>» бесплатно.
                <?php endif ?>
                <?php echo $this->game->lead ?>
            </p>

            <p>
                <?php foreach ($this->game->links as $link): ?>
                    <a href="<?php echo $this->baseUrl($link['url']) ?>" class="btn btn-success">
                        Скачать полную версию
                        <strong><?php echo $link['name'] ?></strong>
                    </a>
                <?php endforeach ?>
            </p>
        </div>
    </div>
    <?php endif ?>

    <?php if ($this->related): ?>
    <div class="game-related">
        <h4>Если вам понравилась игра «<?php echo $this->game->name ?>» тогда не пропустите</h4>

        <div class="game-collection clearfix row">
            <?php foreach ($this->related as $k => $item): ?>
                <div class="col-md-6 col-sm-12 col-xs-12">
                    <div class="game-item-list clearfix">
                        <a href="<?php echo $this->url('game', array($item->latin), array('type' => $this->typeLatin, 'brand' => $this->brandLatin)) ?>" class="image pull-left">
                            <img src="<?php echo $this->baseUrl($item->picture) ?>"
                                 alt="<?php echo $item->name ?>" class="img-responsive"/>
                        </a>
                        <div class="box pull-left">
                            <h5><a href="<?php echo $this->url('game', array($item->latin), array('type' => $this->typeLatin, 'brand' => $this->brandLatin)) ?>">
                                    <?php echo $item->name ?></a></h5>
                            <p><?php echo $item->lead ?></p>
                            <?php if ($item->embed && $this->typeLatin != 'browser'): ?>
                                <a href="<?php echo $this->url('game', array($item->latin), array('type' => $this->typeLatin, 'brand' => $this->brandLatin)) ?>#gameOnline" class="label-online">Играть онлайн</a>
                            <?php endif ?>
                        </div>
                    </div>
                </div>

                <?php if (($k + 1) % 2 == 0): ?>
                    <div class="clearfix"></div>
                <?php endif ?>
            <?php endforeach ?>
        </div>

    </div>
    <?php endif ?>
</div>

<script type="text/javascript">

    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
        event.preventDefault();
        return $(this).ekkoLightbox();
    });
</script>