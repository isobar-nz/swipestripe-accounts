<div class="container">
    <div class="row">
        <section class="col-md-10 col-md-offset-1">
            <div class="page-header">
                {$Breadcrumbs}
                <h1>{$Title}</h1>
            </div>
        </section>
    </div>

    <table class="table">
        <tbody>
            <% loop $Orders %>
            <tr>
                <td><h3><a href="{$Link}">Order #{$ID}</a></h3></td>
                <td>
                    <h5>Date
                        <small>{$ConfirmationTime.Date}</small>
                    </h5>
                    <h5>Items
                        <small>{$OrderItems.sum('Quantity')}</small>
                    </h5>
                </td>
                <td>
                    <h3>{$Total.Nice}</h3>
                    <p><a href="{$Link}/receipt">Download Receipt (PDF)</a></p>
                </td>
            </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>
