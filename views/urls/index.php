<?= $this->render_partial('urls/add.php') ?>

<? if ($count === 0): ?>
    <?= MessageBox::info(_('Sie haben noch keine Links gekürzt.')) ?>
<? return; endif; ?>

<table class="default">
    <colgroup>
        <col width="30%">
        <col>
        <col width="10%">
        <col width="24px">
    </colgroup>
    <thead>
        <tr>
            <th><?= ('Gekürzt') ?></th>
            <th><?= _('Original') ?></th>
            <th><?= _('Klicks') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($urls as $url): ?>
        <tr>
            <td>
                <a href="<?= htmlReady($url->shorturl) ?>" target="_blank" class="link-extern">
                    <?= htmlReady($url->shorturl) ?>
                </a>
            </td>
            <td>
                <a href="<?= htmlReady($url->url) ?>" target="_blank" class="link-extern">
                    <?= htmlReady($url->title) ?>
                </a>
            </td>
            <td>
                <?= number_format($url->clicks, 0, ',', '.') ?>
            </td>
            <td class="actions">
                <form action="<?= $controller->link_for("urls/delete/{$url->id}") ?>" method="post">
                    <?= Icon::create('trash')->asInput(tooltip2(_('Link löschen')) + [
                        'data-confirm' => _('Wollen Sie diesen Link wirklich löschen?'),
                    ]) ?>
                </form>
            </td>
        </tr>
    <? endforeach; ?>
    </tbody>
<? if ($count > Config::get()->ENTRIES_PER_PAGE): ?>
    <tfoot>
    </tfoot>
<? endif; ?>
</table>
