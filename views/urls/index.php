<? if ($count === 0): ?>
    <?= MessageBox::info($_('Sie haben noch keine Links gekürzt.')) ?>

    <?= $this->render_partial('urls/add.php') ?>
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
            <th><?= $_('Gekürzt') ?></th>
            <th><?= $_('Original') ?></th>
            <th><?= $_('Klicks') ?></th>
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
            <td title="<?= $_('Stand') . ':' . strftime('%x %X', $url->updated) ?>">
                <?= number_format($url->clicks, 0, ',', '.') ?>
            </td>
            <td class="actions">
                <form action="<?= $controller->delete($url) ?>" method="post">
                    <?= Icon::create('trash')->asInput(tooltip2($_('Link löschen')) + [
                        'data-confirm' => $_('Wollen Sie diesen Link wirklich löschen?'),
                    ]) ?>
                </form>
            </td>
        </tr>
    <? endforeach; ?>
    </tbody>
<? if ($count > Config::get()->ENTRIES_PER_PAGE): ?>
    <tfoot>
        <tr>
            <td colspan="4" class="actions">
                <?= $GLOBALS['template_factory']->render('shared/pagechooser.php', [
                    'num_postings' => $count,
                    'perPage'      => Config::get()->ENTRIES_PER_PAGE,
                    'page'         => $page,
                    'pagelink'     => $controller->indexURL('%u')
                ]) ?>
            </td>
        </tr>
    </tfoot>
<? endif; ?>
</table>
