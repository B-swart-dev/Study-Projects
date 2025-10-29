package com.example.grocery_list_app;

import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Toast;

public class MainActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        final RadioGroup radioGroup =
                findViewById(R.id.radioGroup);
        final RadioButton rdbBrownBread =
                findViewById(R.id.rdbBrownBread);
        final RadioButton rdbWhiteBread =
                findViewById(R.id.rdbWhiteBread);
        final Button button =
                findViewById(R.id.button);
        final CheckBox cbMilk0 =
                findViewById(R.id.cbMilk);
        final CheckBox cbPasta0 =
                findViewById(R.id.cbPasta);
        final CheckBox cbLemons0 =
                findViewById(R.id.cbLemons);
        final CheckBox cbEggs0 =
                findViewById(R.id.cbEggs);
        EditText Multi =
                findViewById(R.id.editTextTextMultiLine);
        ShoppingList s = new ShoppingList();


        button.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Multi.clearComposingText();
                s.strElement = null;
                s.list = null;
                try {
                    s.arrayList.toArray(new String[s.arrayList.size()]);
                    s.F = 0;

                    if(cbMilk0.isChecked()){
                        s.arrayList.add(s.F, "Milk");
                        s.F++;
                    }
                    if(cbLemons0.isChecked()){
                        s.arrayList.add(s.F, "Lemons");
                        s.F++;
                    }
                    if(cbEggs0.isChecked()){
                        s.arrayList.add(s.F, "Eggs");
                        s.F++;
                    }
                    if(cbPasta0.isChecked()){
                        s.arrayList.add(s.F, "Pasta");
                        s.F++;
                    }
                    if(rdbBrownBread.isChecked()){
                        s.arrayList.add(s.F, "Brown Bread");
                        s.F++;
                    }
                    if(rdbWhiteBread.isChecked()){
                        s.arrayList.add(s.F, "White Bread");
                        s.F++;
                    }
                    int intIndex = s.arrayList.size();
                    for(int i = 0; i < intIndex; i++)
                    {
                        s.strElement = s.arrayList.get(i);
                        s.strElement = s.strElement + "\n";
                        if(i==0){
                            s.list = s.strElement;
                        }
                        if(!(i ==0)){
                            s.list = s.list + (s.strElement);
                        }
                    }
                    Multi.setText(s.list.toString());
                }
                catch (Exception ex){
                    Toast.makeText(MainActivity.this, "Please fill out your grocery list before clicking enter", Toast.LENGTH_SHORT).show();
                }
            }
        });
    }

}