<div class="game-collection admin-page clearfix row">

    <div class="col-xs-12">
        <ul class="list-inline list-unstyled admin-toolbar">
        <li>
            <a href="<?php echo $this->url('addGame') ?>" class="btn btn-success btn-sm">Добавить игру</a>
        </li>
        <li>
            <form action="<?php echo $this->url('admin') ?>" method="GET" class="form-inline">
                <div class="form-group">
                    <input type="text" class="form-control input-sm" name="q"
                           value="<?php echo $this->q ?>"
                           placeholder="Поиск по названию"/>
                    <button type="submit" class="btn btn-warning btn-sm">Найти</button>
                </div>
            </form>
        </li>
        <li>
            <a href="<?php echo $this->url('contentList') ?>" class="btn btn-primary btn-sm">Контент</a>
        </li>
        </ul>
    </div>

    <?php foreach ($this->list as $k => $item): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="game-item-admin clearfix" id="game<?php echo $item->id ?>">
                <a href="<?php echo $this->url('game', $item->latin) ?>" class="image">
                    <img src="<?php echo $this->baseUrl($item->picture) ?>" alt="<?php echo $item->name ?>"/>
                </a>
                <div class="caption">
                    <a href="<?php echo $this->url('game', $item->latin) ?>" class="name">
                        <span title="<?php echo htmlspecialchars($item->name) ?>">
                            <?php echo cutTextChar($item->name, 50) ?>
                        </span>
                    </a>
                    <div class="btn-group-xs btn-group">
                        <?php if ($item->isRecommended()): ?>
                            <a href="<?php echo $this->url('upGame', array($item->latin), array('return' => $this->request->serverUrl() . '#game' . $item->id)) ?>" class="btn btn-info">вернуть</a>
                        <?php else: ?>
                            <a href="<?php echo $this->url('upGame', array($item->latin), array('return' => $this->request->serverUrl() . '#game' . $item->id)) ?>" class="btn btn-success">поднять</a>
                        <?php endif ?>
                        <a href="<?php echo $this->url('editGame', $item->latin) ?>"
                           class="btn btn-primary">Редактировать</a>
                        <a href="<?php echo $this->url('removeGame', array($item->latin), array('return' => $this->request->serverUrl())) ?>"
                           onclick="return confirm('Подтверждаете удаление?')"
                           class="btn btn-default">Удалить</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>

    <div class="col-xs-12">
        <div class="admin-toolbar bottom">
            <?php echo $this->renderPager($this->pages, $this->page, array('admin')) ?>
        </div>
    </div>
</div>