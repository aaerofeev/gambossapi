<div class="game-short-view row">
    <div class="col-md-4">
        <a href="<?php echo $this->url('game', $this->game->latin) ?>">
        <img src="<?php echo $this->baseUrl($this->game->picture) ?>"
             alt="<?php echo $this->game->lead ?>" class="img-responsive img-main"/>
        </a>

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
    <div class="col-md-8">
        <h3><a href="<?php echo $this->url('game', $this->game->latin) ?>"><?php echo $this->game->name ?></a></h3>
        <p><?php echo nl2br(cutTextChar($this->game->desc, 500)) ?></p>
    </div>

    <div class="clearfix"></div>

    <?php if ($this->game->screen): ?>
    <div class="col-md-12">
        <div class="row image-list">
            <?php foreach (array_splice($this->game->screen, 0, 4) as $shot): ?>
                <div class="col-md-3 img-view">
                    <a href="<?php echo $this->baseUrl($shot['picture']) ?>"
                       data-toggle="lightbox"
                       data-gallery="gameGallery"
                       data-parent=".image-list"
                       data-game="<?php echo $this->game->id ?>"
                       data-title="<?php echo $this->game->name ?>"
                       data-footer="<?php echo $this->game->lead ?>">
                        <img src="<?php echo $this->baseUrl($shot['picture']) ?>"
                             alt="<?php echo $this->game->lead ?>" class="img-responsive"/>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <?php endif ?>
    <script type="text/javascript">

        $(document).delegate('*[data-toggle="lightbox"][data-game=<?php echo $this->game->id ?>]', 'click', function(event) {
            event.preventDefault();
            return $(this).ekkoLightbox();
        });
    </script>
</div>