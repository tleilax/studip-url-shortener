<form action="<?= $controller->store() ?>" method="post" class="default url-add" autocomplete="off">
    <fieldset>
        <legend class="hide-in-dialog"><?= $_('Link kürzen') ?></legend>

        <label>
            <?= $_('URL, die gekürzt werden soll') ?>
            <input required type="url" name="url" autofocus
                   placeholder="https://">
        </label>

    <? if ($prefix): ?>
        <label class="with-prefix">
            <?= $_('Gewünschter Kurzlink') ?> (<?= $_('optional') ?>)
            <span class="prefix-wrapper">
                <span class="prefix"><?= htmlReady($prefix) ?></span>
                <input type="text" name="keyword"
                       pattern="^\w[\w-]+$">
            </span>
        </label>
    <? else: ?>
        <label>
            <?= $_('Gewünschter Kurzlink') ?> (<?= $_('optional') ?>)
            <input type="text" name="keyword"
                   pattern="^\w[\w-]+$">
        </label>
    <? endif; ?>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept($_('Speichern')) ?>
        <?= Studip\LinkButton::createCancel(
            $_('Abbrechen'),
            $controller->indexURL()
        ) ?>
    </footer>
</form>
