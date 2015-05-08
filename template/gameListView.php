<div class="tile-view-caption"><?php echo $this->title ?></div>
<div class="game-collection clearfix row tile-view">
    <?php if (count($this->list)): ?>
    <?php foreach ($this->list as $k => $item): ?>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="game-item-list clearfix">
                <a href="<?php echo $this->url('game', array($item->latin), array('type' => $this->typeLatin, 'brand' => $this->brandLatin)) ?>" class="image pull-left">
                    <img src="<?php echo $this->baseUrl($item->picture) ?>"
                         alt="<?php echo $item->name ?>" class="img-responsive"/>
                </a>
                <div class="box pull-left">
                    <h5><a href="<?php echo $this->url('game', array($item->latin), array('type' => $this->typeLatin, 'brand' => $this->brandLatin)) ?>">
                        <?php echo $item->name ?></a></h5>
                    <p><?php echo cutTextChar($item->lead, 150) ?></p>
                    <?php if ($item->isOnline() && $this->typeLatin != 'browser'): ?>
                        <a href="<?php echo $this->url('game', array($item->latin), array('type' => $this->typeLatin, 'brand' => $this->brandLatin)) ?>#gameOnline" class="label-online">Играть онлайн</a>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <?php if (($k + 1) % 2 == 0): ?>
            <div class="clearfix"></div>
        <?php endif ?>
    <?php endforeach ?>

    <?php if (!empty($this->pages)): ?>
        <div class="col-md-12">
            <?php echo $this->renderPager($this->pages, $this->page, array('admin')) ?>
        </div>
    <?php endif ?>

    <p class="game-collection-text col-md-12">
        На нашем <a href="<?php echo $this->url('main') ?>">сайте</a> вы можете скачать бесплатно
        игры по прямой ссылке, сразу, без регистрации и оплаты. Игры представлены от официальных издателей.
        Игры на сайте проходят проверку антивирусом.
        Играть <a href="<?php echo $this->url('type', 'browser') ?>">онлайн в браузерные игры</a> с друзьями.
    </p>

    <?php else: ?>
        <h4 class="text-center">По заданым параметрам игр не найдено</h4>
    <?php endif ?>

</div>