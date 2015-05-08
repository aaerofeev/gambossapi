<div class="admin-form row">

    <div class="col-md-12">
        <h2><?php echo $this->title ?></h2>
    </div>

    <form method="POST" action="" enctype="multipart/form-data" class="game-editor">

        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Название игры</label>
                <input type="text" class="form-control" required
                       id="name" value="<?php echo $this->name ?>"
                       maxlength="64" name="name"
                    />
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group">
                <label for="lead">Короткое описание</label>
                <textarea class="form-control" maxlength="256" rows="4" required
                      id="lead" name="lead"><?php echo $this->lead ?></textarea>
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group">
                <label for="desc">Описание</label>
                <textarea class="form-control" maxlength="1024" rows="10" required
                      id="desc" name="desc"><?php echo $this->desc ?></textarea>
            </div>
        </div>

        <div class="col-md-8">
            <div class="form-group">
                <label for="embed">Код онлайн игры</label>
                <textarea class="form-control" rows="4"
                          id="embed" name="embed"><?php echo $this->embed ?></textarea>
            </div>
        </div>

        <div class="clearfix"></div>

        <?php if ($this->picture): ?>
        <div class="col-md-2">
            <img src="<?php echo $this->baseUrl($this->picture) ?>" class="img-responsive img-thumbnail" />
        </div>
        <?php endif ?>


        <div class="col-md-8">
            <div class="form-group">
                <label for="picture">Изображение</label>
                <input type="file" id="picture" name="image[picture]"
                       <?php if (!$this->picture): ?>required<?php endif ?>/>
                <span class="help-block">Размер 250px</span>
            </div>
        </div>

        <div class="clearfix"></div>

        <?php if ($this->thumb): ?>
        <div class="col-md-2">
            <img src="<?php echo $this->baseUrl($this->thumb) ?>" class="img-responsive img-thumbnail" />
        </div>
        <?php endif ?>

        <div class="col-md-8">
            <div class="form-group">
                <label for="thumb">Иконка игры</label>
                <input type="file" id="thumb" name="image[thumb]"/>
                <span class="help-block">Размер 40px</span>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-8">
            <label>Ссылки на скачавание</label>

            <?php for ($i = 0; $i < 3; $i ++): ?>

                <?php if (empty($this->links[$i])):
                    $this->links[$i] = array('name' => '', 'url' => '');
                endif ?>

                <div class="form-group multi-item form-inline">
                    <label>#<?php echo $i + 1 ?></label>

                    <input type="text" name="links[<?php echo $i ?>][name]"
                       value="<?php echo $this->links[$i]['name'] ?>"
                       placeholder="Размер: 15Мб" class="form-control input-md"/>
                    <input type="text" name="links[<?php echo $i ?>][url]"
                       value="<?php echo $this->links[$i]['url'] ?>"
                       placeholder="Ссылка" class="form-control input-md"/>
                </div>

                <div class="clearfix"></div>

            <?php endfor ?>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-12">
            <label>Изображения игрового процесса</label>

            <div class="clearfix"></div>

            <?php for ($i = 0; $i < 6; $i ++): ?>

                <div class="screen-item form-inline form-group col-md-4">
                    <label class="pull-left">#<?php echo $i + 1 ?></label>

                    <input type="file" name="screen[<?php echo $i ?>]" class="input-sm"/>

                    <?php if (!empty($this->screen[$i]['picture'])): ?>
                        <img src="<?php echo $this->baseUrl($this->screen[$i]['picture']) ?>"
                             class="img-responsive img-thumbnail"/>
                    <?php endif ?>
                </div>

            <?php endfor ?>
        </div>

        <div class="clearfix"></div>

        <div class="col-md-8">
            <label>Каталог</label>
            <?php AdminPage::drawCatalog($this->catalog, 'root') ?>
            <span class="help-block">Нужно выбрать полную ветку каталога</span>
        </div>

        <div class="btn-group col-md-12">
            <button type="submit" class="btn btn-primary">
                <?php echo $this->title ?>
            </button>

            <a href="<?php echo $this->url('admin') ?>" class="btn btn-link">
                В панель
            </a>
        </div>

    </form>

</div>