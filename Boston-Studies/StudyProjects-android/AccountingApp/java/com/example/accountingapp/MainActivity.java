package com.example.accountingapp;

import androidx.appcompat.app.AppCompatActivity;

import android.app.Dialog;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Button;
import android.widget.Toast;


public class MainActivity extends AppCompatActivity {
    //Boolean
    boolean m = Boolean.parseBoolean(null);

    //Subprocedures
    public void showDialog() {
        Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.popupview);
        dialog.show();
    }
    public void showTransfer() {
        Dialog dialog = new Dialog(this);
        dialog.setContentView(R.layout.transferpop);
        dialog.show();
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

    }
    public void Transfer(View v){
        //TextViews
        final TextView BalanceText
                = (TextView) findViewById(R.id.BalanceText);

        final TextView BalanceView2
                = (TextView) findViewById(R.id.BalanceView2);

        final EditText Trans = (EditText) findViewById(R.id.edit5);
        Account a = new Account();
        String Test = Trans.getText().toString();
        if (Test.equals("")) {
            Toast.makeText(MainActivity.this, "Please add username", Toast.LENGTH_SHORT).show();
        }
        if(!Test.equals("")){
            if(m == false){
                float adj;
                adj = Float.valueOf(Test);
                a.balance = a.balance - adj;
                a.balance2 = a.balance2 + adj;
                BalanceView2.setText(String.valueOf(a.balance2));
                BalanceText.setText(String.valueOf(a.balance));
            }
            if(m == true){
                float adj;
                adj = Float.valueOf(Test);
                a.balance = a.balance - adj;
                a.balance2 = a.balance2 + adj;
                BalanceView2.setText(String.valueOf(a.balance2));
                BalanceText.setText(String.valueOf(a.balance));
            }
        }

    }
    public void btnLog(View v){
        //TextViews
        final TextView BalanceText
                = (TextView) findViewById(R.id.BalanceText);

        final TextView BalanceView2
                = (TextView) findViewById(R.id.BalanceView2);


        final Button btnLog =
                (Button) findViewById(R.id.btnLog);


        Account a = new Account();
        String Log = btnLog.getText().toString();
        switch (Log){
            case "Login":
                showDialog();
                BalanceText.setText(String.valueOf(a.balance));
                BalanceView2.setText(String.valueOf(a.balance2));
                break;
            case "Log out":
                btnLog.setText("Login");
                BalanceText.setText(String.valueOf(a.balance));
                BalanceView2.setText(String.valueOf(a.balance2));
                break;
        }
    }
    public void btn0(View v){
        //TextViews
        final TextView TextID
                = (TextView) findViewById(R.id.TextID);

        final TextView Holder
                = (TextView) findViewById(R.id.Holder);


        //Edit Text's
        final EditText txtID0
                = (EditText) findViewById(R.id.txtID0);

        Account a = new Account();
        a.id = (EditText)findViewById(R.id.txtID0);
        if (a.id.equals(null)) {
            Toast.makeText(MainActivity.this, "Please add username", Toast.LENGTH_SHORT).show();
        }
        if(!a.id.equals("")){

            Holder.setText(a.id);
            TextID.setText(a.id + " Account1");
            txtID0.setText("Log out");
        }
    }
    public void transfer1(View v){
        final Button btnLog =
                (Button) findViewById(R.id.btnLog);


        String Log = btnLog.getText().toString();
        switch (Log){
            case "Login":
                Toast.makeText(MainActivity.this,"Please login", Toast.LENGTH_SHORT).show();
                break;
            case "Log out":
                m = true;
                showTransfer();
                break;
        }
    }
    public void transfer2(View v){
        final Button btnLog =
                (Button) findViewById(R.id.btnLog);
        String Log = btnLog.getText().toString();
        switch (Log){
            case "Login":
                Toast.makeText(MainActivity.this,"Please login", Toast.LENGTH_SHORT).show();
                break;
            case "Log out":
                m = false;
                showTransfer();
                break;
        }
    }
}