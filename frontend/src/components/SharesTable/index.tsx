import React, {useEffect, useState} from 'react';
import StockClient from '../../clients/StockClient';
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
import {trackPromise} from 'react-promise-tracker';

interface Props {
  user: string;
  stockClient: StockClient;
}

export default function SharesTable({ user, stockClient }: Props) {
  const [shares, setShares] = useState<Share[]>([]);

  useEffect(() => {
    const fetchShares = async () => {
      setShares(await trackPromise(stockClient.getHoldings(user)));
    };

    fetchShares();
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
                <TableCell>Name</TableCell>
                <TableCell>Shares</TableCell>
                <TableCell align="right">Current value</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {shares.map(share => {
                return (<SharesTableItem key={share.id} share={share}/>)
              })}
            </TableBody>
            <TableFooter>
              <TableRow>
                <TableCell align="right" colSpan={3}>
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
