<form action="<?= $controller->link_for('urls/store') ?>" method="post" class="default">
    <fieldset>
        <legend class="hide-in-dialog"><?= _('Link kürzen') ?></legend>

        <div class="two-columns two-to-one">
            <label>
                <input required type="url" name="url" placeholder="<?= _('URL') ?>">
            </label>

            <label>
                <input type="text" name="keyword"
                       pattern="^\w[\w-]+$" placeholder="<?= _('Kurzlink') ?> (<?= _('optional') ?>)">
            </label>
        </div>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern')) ?>
        <?= Studip\LinkButton::createCancel(
            _('Abbrechen'),
            $controller->url_for('urls')
        ) ?>
    </footer>
</form>
