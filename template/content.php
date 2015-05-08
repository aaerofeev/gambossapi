<div class="tile-view-caption">
    <?php echo $this->page->caption ?>
</div>

<div class="content-view clearfix tile-view row">

    <div class="content-head clearfix">
        <?php if ($this->page->image): ?>
            <div class="col-md-3">
                <img src="<?php echo $this->baseUrl($this->page->image) ?>"
                     class="img-responsive" alt="<?php echo $this->page->caption ?>"/>
            </div>

            <div class="col-md-8">
                <p class="lead"><?php echo $this->page->lead ?></p>
            </div>
        <?php else: ?>
            <div class="col-md-12">
                <p class="lead"><?php echo $this->page->lead ?></p>
            </div>
        <?php endif ?>

    </div>

    <div class="col-md-12">
        <div class="content-body">
            <?php echo $this->page->body ?>
        </div>
    </div>

    <p class="game-collection-text col-md-12">
        На нашем <a href="<?php echo $this->url('main') ?>">сайте</a> вы можете скачать бесплатно
        игры по прямой ссылке, сразу, без регистрации и оплаты. Игры представлены от официальных издателей.
        Игры на сайте проходят проверку антивирусом.
        Играть <a href="<?php echo $this->url('type', 'browser') ?>">онлайн в браузерные игры</a> с друзьями.
    </p>

</div>

<script type="text/javascript">

    $(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
        event.preventDefault();
        return $(this).ekkoLightbox();
    });
</script>