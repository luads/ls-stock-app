import React, {FormEvent, useContext, useState} from 'react';
import StockClient from '../../clients/StockClient';
import {
  Grid,
  IconButton,
  InputAdornment,
  TextField,
  Typography
} from '@material-ui/core';
import {Search} from '@material-ui/icons';
import {trackPromise} from 'react-promise-tracker';
import ShareDetail from '../../interfaces/ShareDetail';
import SharePurchaseDialog from '../SharePurchaseDialog';
import {useDisplayAlert} from '../../hooks/useDisplayAlert';
import {AppContext} from '../../context/AppContext';

interface Props {
  stockClient: StockClient;
  reloadShares: any;
  reloadBalance: any;
}

export default function SharePurchase({ stockClient, reloadShares, reloadBalance }: Props) {
  const { user } = useContext(AppContext);
  const displayAlert = useDisplayAlert();
  const [shareToSearch, setShareToSearch] = useState<string>('');
  const [share, setShare] = useState<ShareDetail | null>(null);

  const handleSearch = async (e: FormEvent) => {
    e.preventDefault();

    if (!shareToSearch) {
      return;
    }

    let details = null;

    try {
      details = await trackPromise(stockClient.getShare(user, shareToSearch));
    } catch (error) {
      displayAlert({ severity: 'error', text: error.message });
      return;
    }

    if (!details) {
      displayAlert({ severity: 'warning', text: 'Share not found' });
      return;
    }

    setShare(details);
    setShareToSearch('');
  };

  return (
    <Grid item xs={12}>
      <Typography component="h3" variant="h6" className="card-title">Buy new shares</Typography>

      <form method="POST" onSubmit={handleSearch}>
        <TextField
          label="Share"
          name="symbol"
          variant="outlined"
          size="small"
          onChange={(e) => setShareToSearch(e.target.value)}
          value={shareToSearch}
          InputProps={{
            endAdornment: <InputAdornment position="end">
              <IconButton aria-label="search shares" edge="end" type="submit">
                <Search/>
              </IconButton>
            </InputAdornment>
          }}
        />
      </form>

      <SharePurchaseDialog
        stockClient={stockClient}
        share={share}
        setShare={setShare}
        reloadShares={reloadShares}
        reloadBalance={reloadBalance}
      />
    </Grid>
  );
}
