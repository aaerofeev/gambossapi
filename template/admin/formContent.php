<div class="admin-form row">

    <div class="col-md-12">
        <h2><?php echo $this->title ?></h2>
    </div>

    <form method="POST" action="" enctype="multipart/form-data" class="game-editor">

        <div class="col-md-6">
            <div class="form-group">
                <label for="caption">Заголовок</label>
                <input type="text" class="form-control" required
                       id="caption" value="<?php echo $this->caption ?>"
                       maxlength="64" name="caption"
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

<div class="col-md-12">
<h4>Коллекция</h4>
<pre>
[COLLECTION=Название коллекции]
http://jezzy.ru/game/zov-atlantidyi-sokrovischa-poseydona.html
http://jezzy.ru/game/zagadki-nyu-yorka-sekretyi-mafii.html
http://jezzy.ru/game/legendyi-vostoka-voshodyaschee-solntse.html
http://jezzy.ru/game/poezd-privideniy-duhi-harona-kollektsionnoe-izdanie.html
[/COLLECTION]
</pre>

<h4>Рейтинг</h4>
<pre>
[RATING=Название рейтинга]
http://jezzy.ru/game/zov-atlantidyi-sokrovischa-poseydona.html
http://jezzy.ru/game/zagadki-nyu-yorka-sekretyi-mafii.html
http://jezzy.ru/game/legendyi-vostoka-voshodyaschee-solntse.html
http://jezzy.ru/game/poezd-privideniy-duhi-harona-kollektsionnoe-izdanie.html
[/RATING]
</pre>

<h4>Одна игра</h4>
<pre>
[GAME]http://jezzy.ru/game/poezd-privideniy-duhi-harona-kollektsionnoe-izdanie.html[/GAME]
</pre>
</div>

        <div class="col-md-10">
            <div class="form-group">
                <label for="body">Текст материала</label>
                <textarea class="form-control" rows="25" required
                          id="body" name="body"><?php echo $this->body ?></textarea>
            </div>
        </div>

        <div class="clearfix"></div>

        <?php if ($this->image): ?>
            <div class="col-md-2">
                <img src="<?php echo $this->baseUrl($this->image) ?>" class="img-responsive img-imagenail" />
            </div>
        <?php endif ?>

        <div class="col-md-8">
            <div class="form-group">
                <label for="image">Изображение</label>
                <input type="file" name="image"/>
                <span class="help-block">Размер 400px</span>
            </div>
        </div>

        <div class="btn-group col-md-12">
            <button type="submit" class="btn btn-primary">
                <?php echo $this->title ?>
            </button>

            <a href="<?php echo $this->url('contentList') ?>" class="btn btn-link">
                В панель
            </a>
        </div>

    </form>

</div>