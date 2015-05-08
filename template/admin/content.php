<div class="game-collection admin-page clearfix row">

    <div class="col-xs-12">
        <ul class="list-inline list-unstyled admin-toolbar">
            <li>
                <a href="<?php echo $this->url('addContent') ?>" class="btn btn-success btn-sm">Добавить страницу</a>
            </li>
            <li>
                <a href="<?php echo $this->url('admin') ?>" class="btn btn-primary btn-sm">Список игр</a>
            </li>
        </ul>
    </div>

    <?php foreach ($this->list as $k => $item): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="game-item-admin clearfix" id="game<?php echo $item->id ?>">
                <a href="<?php echo $this->url('contentView', $item->latin) ?>" class="image">
                    <?php if ($item->image): ?>
                    <img src="<?php echo $this->baseUrl($item->image) ?>" class="img-thumbnail img-responsive"
                         alt="<?php echo $item->caption ?>"/>
                    <?php else: ?>
                        <?php echo cutTextChar($item->lead, 100) ?>
                    <?php endif ?>
                </a>
                <div class="caption">
                    <a href="<?php echo $this->url('contentView', $item->latin) ?>" class="name">
                        <span title="<?php echo htmlspecialchars($item->caption) ?>">
                            <?php echo cutTextChar($item->caption, 50) ?>
                        </span>
                    </a>
                    <div class="btn-group-xs btn-group">
                        <a href="<?php echo $this->url('editContent', $item->latin) ?>"
                           class="btn btn-primary">Редактировать</a>
                        <a href="<?php echo $this->url('removeContent', array($item->latin), array('return' => $this->request->serverUrl())) ?>"
                           onclick="return confirm('Подтверждаете удаление?')"
                           class="btn btn-default">Удалить</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>