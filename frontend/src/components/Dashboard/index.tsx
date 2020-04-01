import React, {useContext, useState} from 'react';
import {Button, Grid, LinearProgress, Typography} from '@material-ui/core';
import StockClient from '../../clients/StockClient';
import Balance from '../Balance';
import BalanceTopUp from '../BalanceTopUp';
import SharesTable from '../SharesTable';
import {trackPromise, usePromiseTracker} from 'react-promise-tracker';
import SharePurchase from '../SharePurchase';
import {Alert} from '@material-ui/lab';
import {AppContext} from '../../context/AppContext';
import Share from '../../interfaces/Share';

interface Props {
  logout(): void;
}

export default function Dashboard({ logout }: Props) {
  const { user, alertDetails, displayAlert } = useContext(AppContext);
  const [balance, setBalance] = useState<number>(0);
  const [shares, setShares] = useState<Share[]>([]);

  const stockClient = new StockClient();
  const { promiseInProgress } = usePromiseTracker();

  const reloadShares = async () => {
      setShares(await trackPromise(stockClient.getHoldings(user)));
  };

  return (
    <div className="dashboard">
      <Grid container spacing={3} direction="row" justify="center" alignItems="center">
        <Grid item xs={9}>
          <Typography variant="subtitle1">
            Hello {user}! <Balance balance={balance}/>
          </Typography>
        </Grid>
        <Grid item xs={3}>
          <BalanceTopUp stockClient={stockClient} setBalance={setBalance}/>
          <Button onClick={logout} className="logout">Logout</Button>
        </Grid>
      </Grid>

      <SharesTable shares={shares} reloadShares={reloadShares}/>
      <SharePurchase stockClient={stockClient} reloadShares={reloadShares}/>

      {promiseInProgress && <LinearProgress className="loader"/>}

      {alertDetails ? (
        <Alert severity={alertDetails.severity} className="alerts" onClose={() => displayAlert(null)}>
          {alertDetails.text}
        </Alert>
      ) : ''}
    </div>
  );
}
