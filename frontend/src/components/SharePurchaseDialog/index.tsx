import React, {FormEvent, useContext, useState} from 'react';
import StockClient from '../../clients/StockClient';
import {
  Button,
  Dialog,
  DialogActions,
  DialogContent,
  DialogContentText,
  DialogTitle,
  TextField,
  Typography
} from '@material-ui/core';
import NumberFormat from 'react-number-format';
import ShareDetail from '../../interfaces/ShareDetail';
import {AlertDetails, useDisplayAlert} from '../../hooks/useDisplayAlert';
import {AppContext} from '../../context/AppContext';
import {trackPromise} from 'react-promise-tracker';

interface Props {
  stockClient: StockClient;
  share: ShareDetail | null;
  setShare: any;
  reloadShares: any;
  reloadBalance: any;
}

export default function SharePurchaseDialog({ stockClient, share, setShare, reloadShares, reloadBalance }: Props) {
  const { user } = useContext(AppContext);
  const displayAlert = useDisplayAlert();
  const [sharesToPurchase, setSharesToPurchase] = useState<number>(0);

  const handleModalClose = () => {
    setShare(null);
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();

    if (!share || !sharesToPurchase) {
      return;
    }

    let transactionValue;

    try {
      transactionValue = await trackPromise(stockClient.purchase(user, share.name, sharesToPurchase));
      reloadShares();
      reloadBalance();
    } catch (error) {
      setShare(null);
      displayAlert({ severity: 'error', text: error.message });
      return;
    }

    const alertText = `${sharesToPurchase} ${share.name} share(s) purchesed successfully! 
      Transaction cost: $${transactionValue.toFixed(2)}`;

    setShare(null);

    displayAlert({
      severity: 'success',
      text: alertText,
    } as AlertDetails)
  };

  return (
    <>
      {share ? (
        <Dialog open={true} onClose={handleModalClose} aria-labelledby="form-dialog-title">
          <form method="POST" onSubmit={handleSubmit}>
            <DialogTitle id="form-dialog-title">Buying {share.name} shares</DialogTitle>
            <DialogContent>
              <DialogContentText>
                The unit price for a {share.name} share is <strong>
                <NumberFormat
                  value={share.price.toString()}
                  displayType="text"
                  thousandSeparator={true}
                  decimalScale={2}
                  fixedDecimalScale={true}
                  prefix="$"/>
              </strong>. Please select the quantity that you want to purchase:
              </DialogContentText>
              <TextField
                onChange={(e) => setSharesToPurchase(parseInt(e.target.value))}
                autoFocus
                margin="dense"
                label="Quantity"
                type="number"
                fullWidth
              /><br/>
              <br/>
              <Typography variant="subtitle1">
                Final price: <strong>
                <NumberFormat
                  value={(share.price * sharesToPurchase).toString()}
                  displayType="text"
                  thousandSeparator={true}
                  decimalScale={2}
                  fixedDecimalScale={true}
                  prefix="$"/>
              </strong>
              </Typography>
            </DialogContent>
            <DialogActions>
              <Button onClick={handleModalClose}>
                Cancel
              </Button>
              <Button onClick={handleModalClose} color="primary">
                Process
              </Button>
            </DialogActions>
          </form>
        </Dialog>
      ) : ''}
    </>
  );
}
