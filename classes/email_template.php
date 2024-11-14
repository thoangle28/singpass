<?php
function email_template()
{
    return '<table>
        <thead>
            <tr style="background-color: #1b84ff;">
                <td style="padding: 32px; text-align: center;">
                    {logo}
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: solid 1px #f0f0f0; padding: 32px;">
                    {content}
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr style="background-color: #1b84ff;">
                <td style="padding: 32px; text-align: center;">
                    {dsfsdf}
                </td>
            </tr>
        </tfoot>
    </table>';
}
