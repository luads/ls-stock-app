import React from 'react';
import {TableCell, TableRow} from '@material-ui/core';
import Share from '../../interfaces/Share';
import NumberFormat from 'react-number-format';

interface Props {
  share: Share;
}

export default function SharesTableItem({ share }: Props) {
  return (
    <TableRow>
      <TableCell>{share.name}</TableCell>
      <TableCell>{share.quantity}</TableCell>
      <TableCell align="right">
        {share.value ? (
          <NumberFormat
            value={share.value}
            displayType={'text'}
            thousandSeparator={true}
            decimalScale={2}
            fixedDecimalScale={true}
            prefix={'$'}
          />
        ) : 'Unavailable'}
      </TableCell>
    </TableRow>
  );
}
