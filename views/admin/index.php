<form action="<?= $controller->link_for('admin/store') ?>" method="post" class="default">
    <fieldset>
        <legend><?= _('API Konfiguration') ?></legend>

        <label>
            <?= _('API Endpunkt') ?>
            <input required type="url" name="endpoint"
                   value="<?= htmLReady($endpoint) ?>">
        </label>

        <label>
            <?= _('Signatur') ?>
            <input type="text" name="signature"
                   value="<?= htmlReady($signature) ?>">
        </label>

        <div class="two-columns">
            <label>
                <?= _('Nutzername') ?>
                <input type="text" name="username"
                       value="<?= htmlReady($username) ?>">
            </label>

            <label>
                <?= _('Passwort') ?>
                <input type="text" name="password"
                       value="<?= htmlReady($password) ?>">
            </label>
        </div>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern')) ?>
        <?= Studip\LinkButton::createCancel(
            _('Abbrechen'),
            $controller->link_for('admin')
        ) ?>
    </footer>
</form>
