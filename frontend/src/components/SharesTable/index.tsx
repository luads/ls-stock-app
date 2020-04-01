import React, {useEffect} from 'react';
import {
  Grid,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableFooter,
  TableHead,
  TableRow,
  Typography
} from '@material-ui/core';
import SharesTableItem from '../SharesTableItem';
import Share from '../../interfaces/Share';
import NumberFormat from 'react-number-format';
import StockClient from '../../clients/StockClient';

interface Props {
  shares: Share[];
  stockClient: StockClient;
  reloadShares: any;
  reloadBalance: any;
}

export default function SharesTable({ shares, stockClient, reloadShares, reloadBalance }: Props) {
  useEffect(() => {
    reloadShares();
  }, []);

  const holdingsValue: number | null = shares.filter((share: Share) => share.value).length === shares.length
    ? shares.reduce((total: number, share: Share) => total + share.value!, 0)
    : null;

  return (
    <Grid item xs={12}>
      <Typography component="h3" variant="h6" className="card-title">Your portfolio</Typography>
      {shares.length ? (
        <Paper elevation={1} variant="outlined">
          <Table size="small">
            <TableHead>
              <TableRow>
                <TableCell>Symbol</TableCell>
                <TableCell>Shares</TableCell>
                <TableCell>Operations</TableCell>
                <TableCell align="right">Current value</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {shares.map(share => {
                return (
                  <SharesTableItem
                    key={share.id}
                    share={share}
                    stockClient={stockClient}
                    reloadShares={reloadShares}
                    reloadBalance={reloadBalance}
                  />
                )
              })}
            </TableBody>
            <TableFooter>
              <TableRow>
                <TableCell align="right" colSpan={4}>
                  {holdingsValue ? (
                    <>
                      <strong>Total: </strong>
                      <NumberFormat
                        value={holdingsValue}
                        displayType={'text'}
                        thousandSeparator={true}
                        decimalScale={2}
                        fixedDecimalScale={true}
                        prefix={'$'}
                      />
                    </>
                  ) : 'Total holdings unavailable'}
                </TableCell>
              </TableRow>
            </TableFooter>
          </Table>
        </Paper>
      ) : (
        <Typography variant="subtitle1">No holdings at the moment.</Typography>
      )}
    </Grid>
  );
}
