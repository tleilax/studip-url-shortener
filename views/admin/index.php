<form action="<?= $controller->link_for('admin/store') ?>" method="post" class="default">
    <fieldset>
        <legend><?= _('Konfiguration') ?></legend>

        <label>
            <span class="required"><?= _('API Endpunkt') ?></span>
            <input required type="url" name="endpoint"
                   value="<?= htmLReady($endpoint) ?>">
        </label>

        <label>
            <?= _('Basis-URL des Shorteners') ?>
            <?= tooltipIcon(
                _('Wird hier eine URL angegeben, so wird diese als Präfix vor '
                . 'dem Kurzlink in der Maske zum Erstellen von Links verwendet.')
            ) ?>
            <input type="url" name="prefix"
                   value="<?= htmlReady($prefix) ?>">
        </label>
    </fieldset>

    <fieldset>
        <legend>
            <?= _('Zugangsdaten') ?>
            <small style="margin-left: 1ex">
                <?= _('Es kann entweder eine Signatur oder eine Nutzername/'
                    . 'Passwort-Kombination angegeben werden.') ?>
            </small>
        </legend>

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
